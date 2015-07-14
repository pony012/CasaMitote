<?php
	include_once 'functions.php';
	include_once 'header.php';	
?>
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<form action="createTiposProductos2.php" id="form" method="POST">
					<fieldset>
						<legend>Categoría de Productos</legend>
						<div class="form-group">
							<label for="">Nombre</label>
							<input type="text" class="form-control" placeholder="Nombre" name="nombre">
						</div>
						<div class="form-group">
							<label for="">Área</label>
							<input type="text" class="form-control" placeholder="Área" name="area">
						</div>
						<div class="form-group"><button type="submit" class="btn btn-success">Agregar</button></div>
					</fieldset>
				</form>
			</div>
			<div class="col-md-6">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Área</th>
							<th># de Prod.</th>
							<th>Opciones</th>
						</tr>
					</thead>
					<tbody id="table-body">
		<?php
			$baseMdl = new BaseMdl();

			$stmt = $baseMdl->driver->prepare("SELECT T.nombre, T.area, T.idTipoProducto as id, (
																		SELECT COUNT( P.idProducto ) FROM Productos P 
																		WHERE P.idTipoProducto = T.idTipoProducto) AS count
												FROM TiposProductos AS T");
			
			if (!$stmt->execute()) {
				//No se pudo ejecutar, error en la base de datos
				$returnObj['error'] = array(
											'code' 			=> 1, 
											'description' 	=> $stmt->error
											);
			}else{
				$result = $stmt->get_result();
				if($result->field_count > 0){
					while($row = $result->fetch_array(MYSQLI_ASSOC)){
		?>
						<tr>
							<td><?php echo $row['nombre']?></td>
							<td><?php echo $row['area']?></td>
							<td><?php echo $row['count']?></td>
							<td data-id="<?php echo $row['id']?>"><small class="label label-info">Editar</small> <small class="label label-danger">Eliminar</small></td>
						</tr>
		<?php
					}
				}
			}
			
		?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(function(){
			submitForm($("#form"), function(data){
				var tr = $('<tr>');
				tr.append($("<td>"+data.nombre+"</td>"));
				tr.append($("<td>"+data.area+"</td>"));
				tr.append($("<td>0</td>"));
				tr.append($('<td data-id="'+data.id+'"><small class="label label-info">Editar</small> <small class="label label-danger">Eliminar</small></td>'));
				$("#table-body").append(tr);
			});
		});
	</script>
<?php
	include_once 'footer.php';
?>