<?php
	include_once 'functions.php';
	session_start();

	$cuenta = $_POST['cuenta'];

	$returnObj = array(
		'ok' => false,
		'id' => NULL
		);

	$idUsuario = isset($_SESSION['user'])?(is_numeric($_SESSION['user'])?$_SESSION['user']:NULL):NULL;

	if($idUsuario == NULL){
		/**
		* TODO!!!
		* Catchar la autentificación
		*/
		$returnObj['error'] = array('code'=>3,'description'=>'No hay usuario ingresado en sistema');
		echo json_encode($returnObj);
		exit;
	}

	//Se insertan los precios y nombres de los productos seleccionados
	$productoConsultaMdl = new BaseMdl();
	foreach ($cuenta['productos'] as $key => $producto) {
		$producto['id'] = is_numeric($producto['id'])?$producto['id']:die(json_encode($returnObj));
		$stmtProductoConsulta = $productoConsultaMdl->driver->prepare("SELECT nombre, precio FROM Productos WHERE idProducto = ? AND activo = 1");
		if(!$stmtProductoConsulta->bind_param('i',$producto['id'])){
			$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de select de productos');
			echo json_encode($returnObj);
			exit;
		}else if (!$stmtProductoConsulta->execute()) {
		//No se pudo ejecutar, error en la base de datos
			$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de select de productos');
			echo json_encode($returnObj);
			exit;
		}else{

			$result = $stmtProductoConsulta->get_result();
			if($result->field_count > 0){
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$cuenta['productos'][$key]['precio'] = $row['precio'];
				$cuenta['productos'][$key]['nombre'] = $row['nombre'];
			}else{
				$returnObj['error'] = array('code'=>4,'description'=>'No existe el producto con id '.$producto['id']);
				echo json_encode($returnObj);
				exit;
			}
		}
		$stmtProductoConsulta->close();
	}

	//Se crea o hace update de la cuenta actual
	$baseMdl = new BaseMdl();

	$nombreCuenta 		= $baseMdl->driver->real_escape_string($cuenta['nombre']);
	$grupo 				= $baseMdl->driver->real_escape_string($cuenta['grupo']);
	$comentarioCuenta	= $baseMdl->driver->real_escape_string(str_replace(array("\r\n", "\r", "\n"), "<br />",$cuenta['comentario']));
	$idCuenta			= isset($cuenta['id'])?(is_numeric($cuenta['id'])?$cuenta['id']:NULL):NULL;
	$fechaHora			= date("Y-m-d H:i:s");

	if($idCuenta == NULL){
		$stmtCuenta = $baseMdl->driver->prepare("INSERT INTO Cuentas(idUsuario, fechaHora, pagada, activa, nombre, grupo, comentario)
															VALUES(?, ?, 0, 1, ?, ?, ?)");
		if(!$stmtCuenta->bind_param('issss',$idUsuario, $fechaHora, $nombreCuenta, $grupo, $comentarioCuenta)){
			$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de insert en Cuentas');
			echo json_encode($returnObj);
			exit;
		}else if (!$stmtCuenta->execute()) {
			//No se pudo ejecutar, error en la base de datos
			$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de insert en Cuentas');
			echo json_encode($returnObj);
			exit;
		}else{
			$idCuenta = $stmtCuenta->insert_id;
		}

		$stmtCuenta->close();
	}else{
		$stmtCuenta = $baseMdl->driver->prepare("UPDATE Cuentas 
													SET fechaHora = ?, comentario = ? 
													WHERE idCuenta = ?");
		if(!$stmtCuenta->bind_param('ssi', $fechaHora, $comentarioCuenta, $idCuenta)){
			$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de update en Cuentas');
			echo json_encode($returnObj);
			exit;
		}else if (!$stmtCuenta->execute()) {
			//No se pudo ejecutar, error en la base de datos
			$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de insert en Cuentas');
			echo json_encode($returnObj);
			exit;
		}else{
			$idCuenta = $stmtCuenta->insert_id;
		}

		$stmtCuenta->close();
	}

	//Se insertan los productos en la cuenta
	$productoMdl = new BaseMdl();
	foreach ($cuenta['productos'] as $key => $producto) {
		$producto['id'] 		= is_numeric($producto['id'])?$producto['id']:die(json_encode($returnObj));
		$producto['precio'] 	= is_numeric($producto['precio'])?$producto['precio']:die(json_encode($returnObj));
		$producto['comentario'] = $baseMdl->driver->real_escape_string(str_replace(array("\r\n", "\r", "\n"), "<br />",$producto['comentario']));

		$stmtProducto = $productoMdl->driver->prepare("INSERT INTO ProductosCuenta(idCuenta, idProducto, cantidad, subTotal, comentario)
																	VALUES(?, ?, 1, ?, ?)");
		if(!$stmtProducto->bind_param('iids', $idCuenta, $producto['id'], $producto['precio'], $producto['comentario'])){
			$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de insert en ProductosCuenta');
			echo json_encode($returnObj);
			exit;
		}else if (!$stmtProducto->execute()) {
			//No se pudo ejecutar, error en la base de datos
			$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de insert en ProductosCuenta');
			echo json_encode($returnObj);
			exit;
		}else{
			
		}
		$stmtProducto->close();
	}

	/*
	$returnObj = array();

	$nombre 	= $baseMdl->driver->real_escape_string($_POST['nombre']);
	$comentario = $baseMdl->driver->real_escape_string(str_replace(array("\r\n", "\r", "\n"), "<br />", $_POST['comentario']));
	$id 		= is_numeric($_POST['precio'])?$_POST['precio']:die(json_encode(array('error'=>array('code'=>1,'description'=>'Error en precio'))));
	$categoria 	= is_numeric($_POST['categoria'])?$_POST['categoria']:die(json_encode(array('error'=>array('code'=>2,'description'=>'Error en categoria'))));

	$stmt = $baseMdl->driver->prepare("SELECT nombre FROM TiposProductos WHERE idTipoProducto = ?");
			
	if(!$stmt->bind_param('i',$categoria)){
		//No se pudo bindear el nombre, error en la base de datos
	}else if (!$stmt->execute()) {
		//No se pudo ejecutar, error en la base de datos
	}else{
		$result = $stmt->get_result();
		if($result->field_count > 0){
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$nombreCategoria = $row['nombre'];
		}else{

		}
	}

	$stmt = $baseMdl->driver->prepare("INSERT INTO Productos(idTipoProducto, nombre, comentario, precio) VALUES(?,?,?,?)");
	if(!$stmt->bind_param('issd',$categoria, $nombre, $comentario, $precio)){
		//No se pudo bindear el nombre, error en la base de datos
	}else if (!$stmt->execute()) {
		//No se pudo ejecutar, error en la base de datos
		$returnObj['error'] = array(
									'code' 			=> 0, 
									'description' 	=> $stmt->error
									);
	}else{
		$returnObj['id'] = $stmt->insert_id;
		$returnObj['categoria'] = $nombreCategoria;
		$returnObj['idCategoria'] = $categoria;
		$returnObj['comentario'] = $comentario;
		$returnObj['nombre'] = $nombre;
		$returnObj['precio'] = $precio;
	}
	*/

	$returnObj['ok'] = true;
	$returnObj['id'] = $idCuenta;
	$returnObj['cuenta'] = $cuenta;

	echo json_encode($returnObj);
?>
	