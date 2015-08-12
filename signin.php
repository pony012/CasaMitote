<?php 
	include_once 'functions.php';
	$user = isset($_POST['user'])?$_POST['user']:NULL;
	$pass = isset($_POST['pass'])?$_POST['pass']:NULL;
	if(isset($user) && isset($pass)){
		if(BaseCtrl::startSession($user, $pass)){
			//echo "Hola";	
		}else{
			//echo "Adios";
		}
	}
		

	header("Location: cuenta.php");
	exit;
?>