<?php
	include_once 'functions.php';
	
	session_start();

	$returnObj = array(
		'ok' => false,
		'error' => 0
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

	$mesa1		= isset($_POST['mesa1'])?(is_numeric($_POST['mesa1'])?$_POST['mesa1']:NULL):NULL;
	$mesa2		= isset($_POST['mesa2'])?(is_numeric($_POST['mesa2'])?$_POST['mesa2']:NULL):NULL;
	
	if($mesa1 == NULL || $mesa1 == NULL || $mesa1 == $mesa2){
		$returnObj['error'] = array('code'=>5,'description'=>'Id de cuenta inválida');
		echo json_encode($returnObj);
		exit;
	}else{
		$cuenta1Mdl = new BaseMdl();
		$stmtCuenta1 = $cuenta1Mdl->driver->prepare("SELECT idCuenta FROM `Cuentas` WHERE idCuenta = ? AND activa = 1 AND pagada = 0");
		if(!$stmtCuenta1->bind_param('i',$mesa1)){
			$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de select de cuenta1');
			echo json_encode($returnObj);
			exit;
		}else if (!$stmtCuenta1->execute()) {
		//No se pudo ejecutar, error en la base de datos
			$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de select de cuenta1');
			echo json_encode($returnObj);
			exit;
		}else{
			$result = $stmtCuenta1->get_result();
			if($result->field_count == 0){
				$returnObj['error'] = array('code'=>4,'description'=>'No existen cuenta en la cuenta '.$idCuenta);
				echo json_encode($returnObj);
				exit;
			}
		}
		$stmtCuenta1->close();

		$cuenta2Mdl = new BaseMdl();
		$stmtCuenta2 = $cuenta2Mdl->driver->prepare("SELECT idCuenta FROM `Cuentas` WHERE idCuenta = ? AND activa = 1 AND pagada = 0");
		if(!$stmtCuenta2->bind_param('i',$mesa2)){
			$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de select de cuenta2');
			echo json_encode($returnObj);
			exit;
		}else if (!$stmtCuenta2->execute()) {
		//No se pudo ejecutar, error en la base de datos
			$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de select de cuenta2');
			echo json_encode($returnObj);
			exit;
		}else{
			$result = $stmtCuenta2->get_result();
			if($result->field_count == 0){
				$returnObj['error'] = array('code'=>4,'description'=>'No existen cuenta en la cuenta '.$idCuenta);
				echo json_encode($returnObj);
				exit;
			}
		}
		$stmtCuenta2->close();

		$updateMdl = new BaseMdl();
		$stmtUpdate = $updateMdl->driver->prepare("UPDATE `ProductosCuenta` SET idCuenta = ? WHERE idCuenta = ?");
		if(!$stmtUpdate->bind_param('ii',$mesa1, $mesa2)){
			$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de select de update');
			echo json_encode($returnObj);
			exit;
		}else if (!$stmtUpdate->execute()) {
		//No se pudo ejecutar, error en la base de datos
			$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de select de update');
			echo json_encode($returnObj);
			exit;
		}else{
			$returnObj['selec'] = $mesa1;
		}
		$stmtUpdate->close();

		$stmtUpdate = $updateMdl->driver->prepare("UPDATE `Cuentas` SET activa = 0 WHERE idCuenta = ?");
		if(!$stmtUpdate->bind_param('i', $mesa2)){
			$returnObj['error'] = array('code'=>4,'description'=>'Error en bind_param de select de update');
			echo json_encode($returnObj);
			exit;
		}else if (!$stmtUpdate->execute()) {
		//No se pudo ejecutar, error en la base de datos
			$returnObj['error'] = array('code'=>4,'description'=>'Error en execute de select de update');
			echo json_encode($returnObj);
			exit;
		}else{
			//Se hizo el update con éxito :)
		}
		$stmtUpdate->close();
	}
	
	$returnObj['ok'] = true;

	echo json_encode($returnObj);
?>
