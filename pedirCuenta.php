<?php
	include_once 'functions.php';
	include_once 'escpos-php/Escpos.php';
	include_once 'EnLetras.php';

	session_start();

	$returnObj = array(
		'ok' => false,
		'errorImpresion' => 0
		);

	$idUsuario = isset($_SESSION['data']['User']['idUsuario'])?(is_numeric($_SESSION['data']['User']['idUsuario'])?$_SESSION['data']['User']['idUsuario']:NULL):NULL;

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

	$idCuenta		= isset($_POST['idCuenta'])?(is_numeric($_POST['idCuenta'])?$_POST['idCuenta']:NULL):NULL;
	$subTotalAPagar = 0;
	$totalAPagar	= 0;
	$tieneDescuento = false;
	$descuentoEfec 	= 0;
	$fechaHora		= date("Y-m-d H:i:s");
	setlocale(LC_MONETARY, 'es_MX');

	if($idCuenta == NULL){
		$returnObj['error'] = array('code'=>5,'description'=>'Id de cuenta inválida');
		echo json_encode($returnObj);
		exit;
	}else{
		$cuentaMdl = new BaseMdl();
		$stmtCuenta = $cuentaMdl->driver->prepare("SELECT U.nombres, C.nombre, C.idCuenta, C.descuento, C.tipoDescuento FROM `Cuentas` AS C
													LEFT JOIN Usuario AS U ON U.idUsuario = C.idUsuario 
													WHERE idCuenta = ?");
		if(!$stmtCuenta->bind_param('i',$idCuenta)){
			$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de select de cuenta');
			echo json_encode($returnObj);
			exit;
		}else if (!$stmtCuenta->execute()) {
		//No se pudo ejecutar, error en la base de datos
			$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de select de cuenta');
			echo json_encode($returnObj);
			exit;
		}else{
			$result = $stmtCuenta->get_result();
			if($result->field_count > 0){
				$cuenta = $result->fetch_array(MYSQLI_ASSOC);
			}else{
				$returnObj['error'] = array('code'=>4,'description'=>'No existen cuenta en la cuenta '.$idCuenta);
				echo json_encode($returnObj);
				exit;
			}
		}
		$stmtCuenta->close();

		$productosMdl = new BaseMdl();
		$stmtProductos = $productosMdl->driver->prepare("SELECT PC.idProducto, P.precio, PC.cantidad, P.nombre FROM `ProductosCuenta` AS PC
															LEFT JOIN Productos AS P ON PC.idProducto = P.idProducto
															WHERE idCuenta = ? ORDER BY PC.idProductoCuenta");
		if(!$stmtProductos->bind_param('i',$idCuenta)){
			$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de select de productos');
			echo json_encode($returnObj);
			exit;
		}else if (!$stmtProductos->execute()) {
		//No se pudo ejecutar, error en la base de datos
			$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de select de productos');
			echo json_encode($returnObj);
			exit;
		}else{
			$result = $stmtProductos->get_result();
			if($result->field_count > 0){
				$productos = array();
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
					$productos[] = $row;
					$subTotalAPagar += $row['precio'] * $row['cantidad'];
				}
				if($cuenta['descuento']){
					$tieneDescuento = true;
					if($cuenta['tipoDescuento']){//$
						if($subTotalAPagar - $cuenta['descuento'] > 0)
							$totalAPagar = $subTotalAPagar - $cuenta['descuento'];
						else
							$totalAPagar = 0;
					}else{//%
						$descuentoEfec = $subTotalAPagar*$cuenta['descuento']/100;
						if($subTotalAPagar - $descuentoEfec > 0)
							$totalAPagar = $subTotalAPagar - $descuentoEfec;
						else{
							$totalAPagar = 0;
							$descuentoEfec = $subTotalAPagar;
						}
					}
				}else{
					$totalAPagar = $subTotalAPagar;
				}
			}else{
				$returnObj['error'] = array('code'=>4,'description'=>'No existen productos en la cuenta '.$idCuenta);
				echo json_encode($returnObj);
				exit;
			}
		}
		$stmtProductos->close();
	}

	$pedirCuentaMdl = new BaseMdl();

	try{
		$connector = new FilePrintConnector("/dev/usb/lp0");
		$printer = new Escpos($connector);
		$printer -> text(mb_str_pad('Centro Cultural', 32, " ", STR_PAD_BOTH, 'UTF-8')."\n");
		$printer -> text(mb_str_pad('Casa Mitote', 32, " ", STR_PAD_BOTH, 'UTF-8')."\n");
		$printer -> text(mb_str_pad('Mesa: '.$cuenta['nombre'].' Mesero: '.$cuenta['nombres'], 32, " ", STR_PAD_BOTH, 'UTF-8')."\n");
		$printer -> text(mb_str_pad($fechaHora, 32, " ", STR_PAD_BOTH, 'UTF-8')."\n");
		foreach ($productos as $key => $producto) {
			$producto['nombre'] = substr($producto['nombre'], 0, 20);
			
			$printer -> text(mb_str_pad($producto['nombre'], 20, '.',STR_PAD_RIGHT, 'UTF-8'));
			$printer -> text(mb_str_pad('$'.money_format('%i',$producto['precio']), 12, '.',STR_PAD_LEFT, 'UTF-8'));
			$printer -> text("\n");
			
		}
		$printer -> text("\n");

		$printer -> text(mb_str_pad('Subtotal', 20, '.',STR_PAD_RIGHT, 'UTF-8'));
		$printer -> text(mb_str_pad('$'.money_format('%i',$subTotalAPagar), 12, '.',STR_PAD_LEFT, 'UTF-8'));
		$printer -> text("\n");

		/*AÑADIR MENSAJE DESCUENTO*/
		if($tieneDescuento){
			$printer -> text(mb_str_pad('Descuento', 16, '.',STR_PAD_RIGHT, 'UTF-8'));
			if($descuentoEfec)
				$printer -> text(mb_str_pad($cuenta['descuento'].'% (-$'.money_format('%i',$descuentoEfec).')', 16, '.',STR_PAD_LEFT, 'UTF-8'));
			else
				$printer -> text(mb_str_pad('-$'.money_format('%i',$descuentoEfec), 16, '.',STR_PAD_LEFT, 'UTF-8'));
			$printer -> text("\n");
		}

		$printer -> text(mb_str_pad('Total', 20, '.',STR_PAD_RIGHT, 'UTF-8'));
		$printer -> text(mb_str_pad('$'.money_format('%i',$totalAPagar), 12, '.',STR_PAD_LEFT, 'UTF-8'));
		$printer -> text("\n");

		$letras = new EnLetras(); 
		$letras = strtoupper($letras->ValorEnLetras($totalAPagar,"pesos")); 
		$printer -> text(mb_str_pad($letras, 32, " ", STR_PAD_BOTH, 'UTF-8'));
		$printer -> text("\n\n");
		$printer -> text(mb_str_pad("La cuenta no incluye propina ;)", 32, " ", STR_PAD_BOTH, 'UTF-8'));
		$printer -> text("\n\n");
		$printer -> text(mb_str_pad("Gracias por su preferencia :)", 32, " ", STR_PAD_BOTH, 'UTF-8'));
		$printer -> text("\n");

		$printer -> text(mb_str_pad('', 32, "_", STR_PAD_BOTH, 'UTF-8'));
		$printer -> text("\n\n\n");
		$printer -> cut();
		$printer -> close();
	}catch(Exception $e){
		/**
		* ToDo
		*/
		$returnObj['errorImpresion'] = 1;
	}

	$returnObj['ok'] = true;

	echo json_encode($returnObj);
?>
