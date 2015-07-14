<?php
	include_once 'functions.php';
	
	$baseMdl = new BaseMdl();

	$returnObj = array();

	$nombre = $baseMdl->driver->real_escape_string($_POST['nombre']);
	$area = $baseMdl->driver->real_escape_string($_POST['area']);

	$stmt = $baseMdl->driver->prepare("INSERT INTO TiposProductos(nombre, area) VALUES(?,?)");
	if(!$stmt->bind_param('ss',$nombre, $area)){
		//No se pudo bindear el nombre, error en la base de datos
	}else if (!$stmt->execute()) {
		//No se pudo ejecutar, error en la base de datos
		$returnObj['error'] = array(
									'code' 			=> 0, 
									'description' 	=> $stmt->error
									);
	}else{
		$returnObj['id'] = $stmt->insert_id;
		$returnObj['nombre'] = $nombre;
		$returnObj['area'] = $area;
	}
	echo json_encode($returnObj);
?>
	