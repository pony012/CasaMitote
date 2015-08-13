<?php
	include_once 'functions.php';

	$returnObj = array(
		'ok' => false,
		'error' => 0
		);

	$user = isset($_POST['user'])?$_POST['user']:NULL;
	$pass = isset($_POST['pass'])?$_POST['pass']:NULL;
	if(isset($user) && isset($pass)){
		if(BaseCtrl::startSession($user, $pass)){
			$returnObj['ok'] = true;
		}else{
			$returnObj['ok'] = false;
			$returnObj['error'] = array('code'=>6,'description'=>'No se pudo acceder');
		}
	}

	echo json_encode($returnObj);
?>
