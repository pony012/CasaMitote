<?php
	error_reporting(E_ALL);
	date_default_timezone_set('America/Mexico_City');
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
		*	Verifica si hay una sesion activa
		*/
		public static function isLoged(){
			return isset($_SESSION['data']['User']['idUsuario']);
		}

		/**
		*	Retorna el id del cargo que está logueado
		*/
		public static function getType(){
			return $_SESSION['type'];
		}
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

			$data = BaseCtrl::getUser($user, $pass);

			if($data['Error']==true){
				return false;
			}else{
				session_set_cookie_params(0);
				session_start();
				$_SESSION['data'] = $data;
				return true;
			}
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
							'Permissions' => array(),
							'Error' => false );

			$stmt = $userMdl->driver->prepare("SELECT * FROM Usuario WHERE idUsuario = ? AND activo = 1");
			$stmt->bind_param('i',$_user);
			$stmt->execute();
		
			$result = $stmt->get_result();
			if($result->field_count > 0){
				$result = $result->fetch_array(MYSQLI_ASSOC);
				if(strcmp($result['password'],$pass)==0){
					$output['User'] = $result;

					$stmt = $userMdl->driver->prepare("SELECT P.*
														FROM  `Usuario` AS U
														LEFT JOIN PermisosCuentas AS PC ON PC.idTipoDeCuenta = U.idTipoDeCuenta
														LEFT JOIN Permisos AS P ON P.idPermiso = PC.idPermiso
														WHERE U.idUsuario = ?");
					$stmt->bind_param('i',$_user);
					$stmt->execute();

					$permisos = $stmt->get_result();
					while($permiso = $permisos->fetch_array(MYSQLI_ASSOC)){
						$output['Permissions'][] = $permiso;
					}
				}else{
					$actualUser = $result;

					$steps = 1;
					$found = false;

					$stmt = $userMdl->driver->prepare("SELECT idTipoDeCuentaPadre FROM TiposDeCuentas WHERE idTipoDeCuenta = ?");
					$stmt->bind_param('i',$result['idTipoDeCuenta']);
					$stmt->execute();
					$cuentaPadre = $stmt->get_result();
					$cuentaPadre = $cuentaPadre->fetch_array(MYSQLI_ASSOC);

					while(!is_null($cuentaPadre['idTipoDeCuentaPadre']) && !$found){
						$stmt = $userMdl->driver->prepare("SELECT * FROM Usuario WHERE idTipoDeCuenta = ? AND password = ?");
						$stmt->bind_param('is',$cuentaPadre['idTipoDeCuentaPadre'], $_pass);
						$stmt->execute();
						$user = $stmt->get_result();
						$user = $user->fetch_array(MYSQLI_ASSOC);
						if($user){
							//$user = $user->fetch_array();
							$found = true;
						}else{
							$steps++;
							$stmt = $userMdl->driver->prepare("SELECT idTipoDeCuentaPadre FROM TiposDeCuentas WHERE idTipoDeCuenta = ?");
							$stmt->bind_param('i',$cuentaPadre['idTipoDeCuentaPadre']);
							$stmt->execute();
							$cuentaPadre = $stmt->get_result();
							$cuentaPadre = $cuentaPadre->fetch_array(MYSQLI_ASSOC);
						}
					}
					if($found){
						$output['User'] = $actualUser;
						$output['Parent'] = $user;
						$output['HasParent'] = true;
						$stmt = $userMdl->driver->prepare("SELECT P.*
															FROM  `Usuario` AS U
															LEFT JOIN PermisosCuentas AS PC ON PC.idTipoDeCuenta = U.idTipoDeCuenta
															LEFT JOIN Permisos AS P ON P.idPermiso = PC.idPermiso
															WHERE U.idUsuario = ?");
						$stmt->bind_param('i',$output['User']['idUsuario']);
						$stmt->execute();

						$permisos = $stmt->get_result();
						while($permiso = $permisos->fetch_array(MYSQLI_ASSOC)){
							$output['Permissions'][] = $permiso;
						}
					}else{
						$output['Error'] = true;
					}
				}
			}
			if($output['Error'] == false){
				unset($output['User']['password']);
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

	function mb_str_pad($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT, $encoding = NULL){
		$encoding = $encoding === NULL ? mb_internal_encoding() : $encoding;
		$padBefore = $dir === STR_PAD_BOTH || $dir === STR_PAD_LEFT;
		$padAfter = $dir === STR_PAD_BOTH || $dir === STR_PAD_RIGHT;
		$pad_len -= mb_strlen($str, $encoding);
		$targetLen = $padBefore && $padAfter ? $pad_len / 2 : $pad_len;
		$strToRepeatLen = mb_strlen($pad_str, $encoding);
		$repeatTimes = ceil($targetLen / $strToRepeatLen);
		$repeatedString = str_repeat($pad_str, max(0, $repeatTimes)); // safe if used with valid utf-8 strings
		$before = $padBefore ? mb_substr($repeatedString, 0, floor($targetLen), $encoding) : '';
		$after = $padAfter ? mb_substr($repeatedString, 0, ceil($targetLen), $encoding) : '';
		return $before . $str . $after;
	}
?>
