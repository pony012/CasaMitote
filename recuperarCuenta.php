<?php
	include_once 'functions.php';
	
	session_start();

	$returnObj = array(
		'ok' => false,
		'error' => 0
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

	$user		= isset($_POST['user'])?(is_numeric($_POST['user'])?$_POST['user']:NULL):NULL;
	$idCuenta 	= isset($_POST['idCuenta'])?(is_numeric($_POST['idCuenta'])?$_POST['idCuenta']:NULL):NULL;
	
	if($user == NULL || $idCuenta == NULL){
		$returnObj['error'] = array('code'=>5,'description'=>'Id de cuenta inválida');
		echo json_encode($returnObj);
		exit;
	}else{
		$user = BaseCtrl::getUser($user, $_POST['pass']);
		if($user['Error']){
			$returnObj['error'] = array('code'=>6,'description'=>'No se pudo acceder');
			echo json_encode($returnObj);
			exit;
		}else{
			$tienePermiso = false;
			foreach($user['Permissions'] as $k => $permiso){
				if(strcmp($permiso['nombre'], 'RecuperarCuenta')==0){
					$tienePermiso = true;
					break;
				}
			}
			if(!$tienePermiso){
				$returnObj['error'] = array('code'=>7,'description'=>'No se tienen los permisos suficientes');
				echo json_encode($returnObj);
				exit;
			}else{
				$updateMdl = new BaseMdl();
				
				//Se checa que no esté duplicada
				$stmtUpdate = $updateMdl->driver->prepare("SELECT nombre FROM `Cuentas` WHERE idCuenta = ? AND activa = 1 AND pagada = 1");
				if(!$stmtUpdate->bind_param('i', $idCuenta)){
					$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de select');
					echo json_encode($returnObj);
					exit;
				}else if (!$stmtUpdate->execute()) {
				//No se pudo ejecutar, error en la base de datos
					$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de select');
					echo json_encode($returnObj);
					exit;
				}else{
					$result = $stmtUpdate->get_result();
					if($result->field_count > 0){
						$row = $result->fetch_array(MYSQLI_ASSOC);
						$nombre = $row['nombre'];
					}
				}
				$stmtUpdate->close();

				$stmtUpdate = $updateMdl->driver->prepare("SELECT count(*) as count FROM `Cuentas` WHERE nombre = ? AND activa = 1 AND pagada = 0");
				if(!$stmtUpdate->bind_param('s', $nombre)){
					$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de select');
					echo json_encode($returnObj);
					exit;
				}else if (!$stmtUpdate->execute()) {
				//No se pudo ejecutar, error en la base de datos
					$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de select');
					echo json_encode($returnObj);
					exit;
				}else{
					$result = $stmtUpdate->get_result();
					if($result->field_count > 0){
						$row = $result->fetch_array(MYSQLI_ASSOC);
						$count = $row['count'];
					}
				}
				$stmtUpdate->close();

				$cambiarNombre = false;
				if($count>0){
					$nombre = $nombre.'_'.substr(md5(time()),0,4);
					$cambiarNombre = true;
				}
				
				if($cambiarNombre){
					$stmtUpdate = $updateMdl->driver->prepare("UPDATE `Cuentas` SET activa = 1, pagada = 0, nombre = ?, grupo = ? WHERE idCuenta = ?");
					if(!$stmtUpdate->bind_param('ssi', $nombre, $nombre, $idCuenta)){
						$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de select de update');
						echo json_encode($returnObj);
						exit;
					}else if (!$stmtUpdate->execute()) {
					//No se pudo ejecutar, error en la base de datos
						$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de select de update');
						echo json_encode($returnObj);
						exit;
					}else{
						$returnObj['selec'] = $idCuenta;
					}
					$stmtUpdate->close();
				}else{
					$stmtUpdate = $updateMdl->driver->prepare("UPDATE `Cuentas` SET activa = 1, pagada = 0 WHERE idCuenta = ?");
					if(!$stmtUpdate->bind_param('i', $idCuenta)){
						$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de select de update');
						echo json_encode($returnObj);
						exit;
					}else if (!$stmtUpdate->execute()) {
					//No se pudo ejecutar, error en la base de datos
						$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de select de update');
						echo json_encode($returnObj);
						exit;
					}else{
						$returnObj['selec'] = $idCuenta;
					}
					$stmtUpdate->close();
				}
			}
		}
	}
	
	$returnObj['ok'] = true;

	echo json_encode($returnObj);
?>
