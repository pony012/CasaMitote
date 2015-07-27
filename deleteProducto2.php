<?php
	include_once 'functions.php';
	
	$baseMdl = new BaseMdl();
	$baseMdl2 = new BaseMdl();

	$returnObj = array();

	$id = is_numeric($_POST['idEliminar'])?$_POST['idEliminar']:die(json_encode(array('error'=>array('code'=>3,'description'=>'Error en id de producto'))));
	
	$stmt = $baseMdl->driver->prepare("UPDATE Productos SET 
										activo = 0
										WHERE idProducto = ?");
	if(!$stmt->bind_param('i', $id)){
		//No se pudo bindear el nombre, error en la base de datos
	}else if (!$stmt->execute()) {
		//No se pudo ejecutar, error en la base de datos
		$returnObj['error'] = array(
									'code' 			=> 0, 
									'description' 	=> $stmt->error
									);
	}else{
		$returnObj['id'] = $id;
	}
	
	echo json_encode($returnObj);
	

?>
	