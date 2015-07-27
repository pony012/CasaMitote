<?php
	include_once 'functions.php';
	
	$baseMdl = new BaseMdl();
	$baseMdl2 = new BaseMdl();

	$returnObj = array();

	$id 		= is_numeric($_POST['idModal'])?$_POST['idModal']:die(json_encode(array('error'=>array('code'=>3,'description'=>'Error en id de producto'))));
	$nombre 	= $baseMdl->driver->real_escape_string($_POST['nombreModal']);
	$comentario = $baseMdl->driver->real_escape_string(str_replace(array("\r\n", "\r", "\n"), "<br />", $_POST['comentarioModal']));
	$precio 	= is_numeric($_POST['precioModal'])?$_POST['precioModal']:die(json_encode(array('error'=>array('code'=>1,'description'=>'Error en precio'))));
	$categoria 	= is_numeric($_POST['categoriaModal'])?$_POST['categoriaModal']:die(json_encode(array('error'=>array('code'=>2,'description'=>'Error en categoria'))));

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

	$stmt = $baseMdl->driver->prepare("UPDATE Productos SET 
										idTipoProducto = ?, 
										nombre = ?, 
										comentario = ?, 
										precio = ? 
										WHERE idProducto = ?");
	if(!$stmt->bind_param('issdi',$categoria, $nombre, $comentario, $precio, $id)){
		//No se pudo bindear el nombre, error en la base de datos
	}else if (!$stmt->execute()) {
		//No se pudo ejecutar, error en la base de datos
		$returnObj['error'] = array(
									'code' 			=> 0, 
									'description' 	=> $stmt->error
									);
	}else{
		$returnObj['id'] = $id;
		$returnObj['categoria'] = $nombreCategoria;
		$returnObj['idCategoria'] = $categoria;
		$returnObj['comentario'] = $comentario;
		$returnObj['nombre'] = $nombre;
		$returnObj['precio'] = $precio;
	}
	
	echo json_encode($returnObj);
	

?>
	