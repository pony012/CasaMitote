<?php
	include_once 'functions.php';
	
	$baseMdl = new BaseMdl();
	$baseMdl2 = new BaseMdl();

	$returnObj = array();

	$nombre 	= $baseMdl->driver->real_escape_string($_POST['nombre']);
	$comentario = $baseMdl->driver->real_escape_string(str_replace(array("\r\n", "\r", "\n"), "<br />", $_POST['comentario']));
	$precio 	= is_numeric($_POST['precio'])?$_POST['precio']:die(json_encode(array('error'=>array('code'=>1,'description'=>'Error en precio'))));
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
	
	echo json_encode($returnObj);
	

?>
	