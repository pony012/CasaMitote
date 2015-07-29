<?php 
	include_once 'functions.php';
	BaseCtrl::killSession();
		

	header("Location: cuenta.php");
	exit;
?>