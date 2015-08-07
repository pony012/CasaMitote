<?php
	include_once 'functions.php';
	include_once 'escpos-php/Escpos.php';
	session_start();

	$cuentas = $_POST['cuentas'];

	$returnObj = array(
		'ok' => false,
		'selec' => NULL,
		'ids' => array(),
		'pedirLogin' => 0,
		'errorImpresion' => 0
		);

	$idUsuario = isset($_SESSION['user'])?(is_numeric($_SESSION['user'])?$_SESSION['user']:NULL):NULL;

	if($idUsuario == NULL){
		/**
		* TODO!!!
		* Catchar la autentificación
		*/
		$returnObj['error'] = array('code'=>3,'description'=>'No hay usuario ingresado en sistema');
		$returnObj['pedirLogin'] = 1;
		echo json_encode($returnObj);
		exit;
	}

	foreach ($cuentas as $iterCuenta => $cuenta) {
		$cuenta['productos'] = (isset($cuenta['productos'])?$cuenta['productos']:array());
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
		$activa				= count($cuenta['productos'])>0?1:0;

		if($idCuenta == NULL && $activa == 1){
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
														SET fechaHora = ?, comentario = ?, activa = ?
														WHERE idCuenta = ?");
			if(!$stmtCuenta->bind_param('ssii', $fechaHora, $comentarioCuenta, $activa, $idCuenta)){
				$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de update en Cuentas');
				echo json_encode($returnObj);
				exit;
			}else if (!$stmtCuenta->execute()) {
				//No se pudo ejecutar, error en la base de datos
				$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de insert en Cuentas');
				echo json_encode($returnObj);
				exit;
			}else{
				//Se hizo update con éxito :)
			}
			$stmtCuenta->close();
		}

		//Se eliminan los productos para actualizar la cuenta.
		$eliminarMdl = new BaseMdl();
		$stmtEliminar = $eliminarMdl->driver->prepare("DELETE FROM ProductosCuenta WHERE idCuenta = ?");
		if(!$stmtEliminar->bind_param('i', $idCuenta)){
			$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de delete en ProductosCuenta');
			echo json_encode($returnObj);
			exit;
		}else if (!$stmtEliminar->execute()) {
			//No se pudo ejecutar, error en la base de datos
			$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de delete en ProductosCuenta');
			echo json_encode($returnObj);
			exit;
		}else{
			//Se hizo delete con éxito :)
		}

		//Se insertan los productos en la cuenta
		
		$productoMdl = new BaseMdl();
		
		$pedir = array();
		foreach ($cuenta['productos'] as $key => $producto) {
			if($producto['pedido'] == 0){
				$producto['area'] = explode(',', $producto['area']);
				foreach ($producto['area'] as $k => $area) {
					if(!isset($area)){
						$pedir[$area] = array();
					}
					$pedir[$area][] = $producto;	
				}
			}
			
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
		foreach ($pedir as $key => $area) {
			if(stristr($key, 'Barra')){
				try{
					$connector = new FilePrintConnector("/dev/usb/lp0");
					$printer = new Escpos($connector);
					$printer -> text(str_pad($cuenta['nombre'], 32, " ", STR_PAD_BOTH)."\n");
					$printer -> text(str_pad($fechaHora, 32, " ", STR_PAD_BOTH)."\n");
					if(!empty($cuenta['comentario'])){
						$cuenta['comentario'] = str_replace("\n", "\n  ", $cuenta['comentario']);
						$printer -> text('*'.$cuenta['comentario']."\n");
					}
					foreach ($area as $key => $producto) {
						$printer -> text('•'.$producto['nombre']."\n");
						if(!empty($producto['comentario'])){
							$producto['comentario'] = str_replace("\n", "\n  ", $producto['comentario']);
							$printer -> text('  '.$producto['comentario']."\n");
						}
					}
					$printer -> text(str_pad('', 32, "_", STR_PAD_BOTH));
					$printer -> text("\n\n\n");
					$printer -> cut();
					$printer -> close();
				}catch(Exception $e){
					/**
					* ToDo
					*/
					$returnObj['errorImpresion'] = 1;
				}
			}else if(stristr($key, 'Cocina')==0){
				try{
					$connector = new FilePrintConnector("/dev/usb/lp0");
					$printer = new Escpos($connector);
					$printer -> text(str_pad($cuenta['nombre'], 32, " ", STR_PAD_BOTH)."\n");
					$printer -> text(str_pad($fechaHora, 32, " ", STR_PAD_BOTH)."\n");
					if(!empty($cuenta['comentario'])){
						$cuenta['comentario'] = str_replace("\n", "\n  ", $cuenta['comentario']);
						$printer -> text('*'.$cuenta['comentario']."\n");
					}
					foreach ($area as $key => $producto) {
						$printer -> text('•'.$producto['nombre']."\n");
						if(!empty($producto['comentario'])){
							$producto['comentario'] = str_replace("\n", "\n  ", $producto['comentario']);
							$printer -> text('  '.$producto['comentario']."\n");
						}
					}
					$printer -> text(str_pad('', 32, "_", STR_PAD_BOTH));
					$printer -> text("\n\n\n");
					$printer -> cut();
					$printer -> close();
				}catch(Exception $e){
					/**
					* ToDo
					*/
					$returnObj['errorImpresion'] = 1;
				}
			}
		}
		array_push($returnObj['ids'], $idCuenta);
		if($cuenta['selec']==1){
			$returnObj['selec'] = $idCuenta;
		}
	}

	$returnObj['ok'] = true;

	echo json_encode($returnObj);
?>
	