<?php

	/**
	* Modelo Base
	* Clase base para los modelos que serán usados en la aplicación
	* @version 0.1
	*/
	class BaseMdl
	{
		public $driver;
		
		/**
		 *	Crea el driver necesario para la conexión a la base de datos
		 *	@param string	$server	Dirección donde está alojada la base de datos
		 *	@param string	$user	Usuario de la base da datos
		 *	@param string	$pass	Contraseña del usuario con el que se accederá la base de datos
		 *	@param string	$db		Nombre de la base de datos
		 *	@return true si se pudo crear el driver, false en caso contrario
		 */
		final function setDriver($server, $user, $pass, $db)
		{
			//TODO
			//Cargar las configuraciones de la bdd y crear el driver
			$mysqli = new mysqli($server,$user,$pass,$db);
			if($mysqli->connect_error)
				return false;
			$this->driver = $mysqli;
			return true;
		}	

		function __construct(){
			require_once 'config.php';
			$this->setDriver(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
		}
	}

	/**
	* Controlador Base
	* Clase base para los controladores que serán creados en la aplicación
	* @version 0.1
	*/
	class BaseCtrl
	{
		/** Donde se guardará el modelo */
		protected $model;
		protected $session;
		
		/**
		*	Inicia una sesion y retorna true si se inició, false si ya existía una activa
		*	En caso de que el usuario esté en la base de datos, se guardará en la sesión el nombre del usuario
		*	y su tipo.
		*	@param string $user
		*	@param string $pass
		*	@return bool
		*/
		public static function startSession($user, $pass){
			if (BaseCtrl::isLoged()){
				return true;
			}
			if (empty($user) || empty($pass)){
				return false;
			}

			$userMdl = new BaseMdl();

			$_user	= $userMdl;
			$_pass	= $userMdl->driver->real_escape_string($pass);

			$stmt = $userMdl->driver->prepare("SELECT * FROM Usuario WHERE idUsuario = ?");
			if(!$stmt->bind_param('i',$_user)){
				//No se pudo bindear el nombre, error en la base de datos
			}else if (!$stmt->execute()) {
				//No se pudo ejecutar, error en la base de datos
			}else{
				$result = $stmt->get_result();
				if($result->field_count > 0){
					$result = $result->fetch_array();
					if(strcmp($result['password'],$pass)==0){
						//md5(md5("1234")."astrum1234".md5("astr"))
						//b956f5207a5f0bfa514292171f1c285f
						$_SESSION['user'] = $user;
						//$_SESSION['pass'] = $pass;
						$_SESSION['type'] = $result['IDCargo'];
						$_SESSION['IDEmpleado'] = $result['IDEmpleado'];
						return true;
					}else{
						//Cargar vista de fallo de contraseña
						
					}
				}else{
					//No se encontró usuario con ese nombre :(
					
				}
			}
			
			return false;
		}

		/**
		*	Obtiene el objeto usuario, así como sus permisos
		*/
		public static function getUser($user, $pass){
			$userMdl = new BaseMdl();
			$_user	= $user;
			$_pass	= $userMdl->driver->real_escape_string($pass);

			$output = array('User' => null,
							'Parent' => null,
							'HasParent' => false,
							'Error' => false );

			$stmt = $userMdl->driver->prepare("SELECT * FROM Usuario WHERE idUsuario = ? AND activo = 1");
			$stmt->bind_param('i',$_user);
			$stmt->execute();
		
			$result = $stmt->get_result();
			if($result->field_count > 0){
				$result = $result->fetch_array();
				if(strcmp($result['password'],$pass)==0){
					$output['User'] = $result;
				}else{
					$actualUser = $result;

					$steps = 1;
					$found = false;

					$stmt = $userMdl->driver->prepare("SELECT idTipoDeCuentaPadre FROM TiposDeCuentas WHERE idTipoDeCuenta = ?");
					$stmt->bind_param('i',$result['idTipoDeCuenta']);
					$stmt->execute();
					$cuentaPadre = $stmt->get_result();
					$cuentaPadre = $cuentaPadre->fetch_array();

					while(!is_null($cuentaPadre['idTipoDeCuentaPadre']) && !$found){
						$stmt = $userMdl->driver->prepare("SELECT * FROM Usuario WHERE idTipoDeCuenta = ? AND password = ?");
						$stmt->bind_param('is',$cuentaPadre['idTipoDeCuentaPadre'], $pass);
						$stmt->execute();
						$user = $stmt->get_result();
						
						if($user->field_count > 0){
							$user = $user->fetch_array();
							$found = true;
						}else{
							$steps++;
							$stmt = $userMdl->driver->prepare("SELECT idTipoDeCuentaPadre FROM TiposDeCuentas WHERE idTipoDeCuenta = ?");
							$stmt->bind_param('i',$user['idTipoDeCuenta']);
							$stmt->execute();
							$cuentaPadre = $stmt->get_result();
							$cuentaPadre = $cuentaPadre->fetch_array();
						}
					}
					if($found){
						$output['User'] = $actualUser;
						$output['Parent'] = $user;
						$output['HasParent'] = true;
					}else{
						$output['Error'] = true;
					}
				}
			}

			return $output;
		}

		/**
		*	Destruye una sesion
		*/
		public static function killSession(){
			session_start();

			session_unset();
			session_destroy();
			
			setcookie(session_name(), '', time()-3600);
		}

		/**
		*	Verifica si hay una sesion activa
		*/
		/*
		public static function isLoged(){
			return isset($_SESSION['user']);
		}
		*/

		/** 
		 *	Valida que una cadena sea un número, retorna la cadena si lo es, en caso de no serlo returna una cadena vacía
		 *	@param string $data
		 *	@return string $data
		 */
		public static function validateNumber($data){
			if(is_numeric($data))
				return $data;
			return "";
		}

		/**
		 *	Valida que una cadena esté limpia, si es así la retorna, en caso de no estarlo, retornará una cadena vacía
		 *	@param string $data
		 *	@return string $data
		 */
		public static function validateText($data){
			//verificamos si es un arreglo
			if(is_string($data)){
				//saco el tam en caracteres del valor
				$tam=strlen($data);
				//verificamos que exista, que no este vacio, que si tam sea menor o igual a 200
				//que no contenga caracteres de escape, quitamos barras y escapamos comillas y quitamos tags
				if(!(
					isset($data) && 
					$data!="" && 
					$tam<=200 && 
					$tam==strlen($data=trim($data)) && 
					$tam==strlen($data=stripslashes($data)) && 
					$tam==strlen($data=addslashes($data)) &&
					$tam==strlen($data=strip_tags($data))
					)
				){
					//regresamos cadena vacía en caso de que la cadena no cumpla la validacion
					return "";
				}
			}
			//todo resultó perfecto
			return $data;
		}
		
		/**
		*	Valida que la cadena de texto sea un correo válido, retorna la cadena sin espacios al inicio o al final si lo es, en caso de no serlo returna una cadena vacía
		*	@param string $data
		*	@return string $data
		*/
		public static function validateEmail($data){
			$data = trim($data);
			$regex = "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/";
			if(preg_match($regex, $data))
				return $data;
			return "";
		}

		/**
		*	Valida que la cadena de texto sea un teléfono, retorna la cadena sin espacios al inicio o al final si lo es, en caso de no serlo returna una cadena vacía
		*	@param string $data
		*	@return string $data	
		*/
		public static function validatePhone($data){
			$data = trim($data);
			$regex = "/^(\+\d{1,4}[- ])?(\d+([ -])*)+$/";
			if(preg_match($regex, $data))
				return $data;
			return "";
		}

		/**
		*	Valida que la cadena de texto sea un nombre válido, retorna la cadena sin espacios al inicio o al final si lo es, en caso de no serlo returna una cadena vacía
		*	@param string $data
		*	@return string $data	
		*/
		public static function validateName($data){
			$data = trim($data);
			if(strcmp(substr($data,-1)," ")!=0)
				$data.=' ';
			$regex = "/^([a-zA-ZáéíóúÁÉÍÓÚ]+ ?){1,5}$/";
			if(preg_match($regex, $data))
				return $data;
			return "";
		}
		/**
		*	Valida que la cadena de texto sea una fecha válida, retorna la cadena sin espacios al inicio o al final si lo es, en caso de no serlo returna una cadena vacía
		*	@param string $data
		*	@return string $data	
		*/
		public static function validateDate($data){
			$data = trim($data);
			$d = DateTime::createFromFormat('Y-m-d', $data);
    		return ($d && $d->format('Y-m-d') == $data)?$data:"";
		}

		public static function validateDateHour($data){
			$data = trim($data);
			$d = DateTime::createFromFormat('Y-m-d H:i:s', $data);
    		return ($d && $d->format('Y-m-d H:i:s') == $data)?$data:"";
		}
		
		public static function validateNumericArray($data){
			$result = array();
			
			foreach($data as $key=>$value){
				if( strlen(BaseCtrl::validateNumber($value)) == 0 ){
					array_push($result, $key);
				}
			}
			return $result;
		}

		private function utf8_encode_deep(&$input) {
		    if (is_string($input)) {
		        $input = utf8_encode($input);
		    } else if (is_array($input)) {
		        foreach ($input as &$value) {
		            $this->utf8_encode_deep($value);
		        }

		        unset($value);
		    } else if (is_object($input)) {
		        $vars = array_keys(get_object_vars($input));

		        foreach ($vars as $var) {
		            $this->utf8_encode_deep($input->$var);
		        }
		    }
		}

		public function json_encode($value){
			$this->utf8_encode_deep($value);
			return json_encode($value);
		}

		/**
		* Construye un controlador Base
		*/
		function __construct(){
			require_once 'config.php';
			
			/*
			$this->session = array(
				'isLoged'=>BaseCtrl::isLoged(),
				'user'=>isset($_SESSION['user'])?$_SESSION['user']:NULL,
				'IDEmpleado' => isset($_SESSION['IDEmpleado'])?$_SESSION['IDEmpleado']:NULL,
				'controller'=>isset($_GET['ctrl'])?$_GET['ctrl']:'index',
				'action'=>'',
			);
			*/

		}
	}
?>
