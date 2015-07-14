<?php
	include_once 'functions.php';
	include_once 'header.php';	
?>
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<form action="createProductos2.php" id="form" method="POST">
					<fieldset>
						<legend>Productos</legend>
						<div class="form-group">
							<label for="nombre">Nombre</label>
							<input type="text" class="form-control" placeholder="Nombre" name="nombre" id="nombre">
						</div>
						<div class="form-group">
							<label for="precio">Precio</label>
							<input type="text" class="form-control" placeholder="Precio" name="precio" id="precio">
						</div>
						<div class="form-group">
							<label for="categoria">Categoria</label>
							<select class="form-control" name="categoria" id="categoria">
								<option value="">Selecciona una categoria</option>
		<?php
			$baseMdl = new BaseMdl();

			$stmt = $baseMdl->driver->prepare("SELECT nombre, area, idTipoProducto as id FROM TiposProductos");
			
			if (!$stmt->execute()) {
				//No se pudo ejecutar, error en la base de datos
			}else{
				$result = $stmt->get_result();
				if($result->field_count > 0){
					while($row = $result->fetch_array(MYSQLI_ASSOC)){
		?>
								<option value="<?php echo $row['id']?>"><?php echo $row['nombre']?></option>
		<?php
					}
				}
			}
			
		?>								
							</select>
						</div>
						<div class="form-group">
							<label for="comentario">Comentario</label>
							<textarea class="form-control" name="comentario" id="comentario" placeholder="Opcional..."></textarea>
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
							<th>Categoría</th>
							<th>Precio ($)</th>
							<th>Comentario</th>
							<th>Opciones</th>
						</tr>
					</thead>
					<tbody id="table-body">
		<?php
			$baseMdl = new BaseMdl();

			$stmt = $baseMdl->driver->prepare("SELECT T.nombre AS categoria, P.nombre, P.comentario, P.precio, P.idProducto AS id FROM 
												TiposProductos AS T
												RIGHT JOIN Productos AS P
												ON T.idTipoProducto = P.idTipoProducto");
			
			if (!$stmt->execute()) {
				
			}else{
				$result = $stmt->get_result();
				if($result->field_count > 0){
					while($row = $result->fetch_array(MYSQLI_ASSOC)){
		?>
						<tr>
							<td><?php echo $row['nombre']?></td>
							<td><?php echo $row['categoria']?></td>
							<td><?php echo number_format($row['precio'])?></td>
							<td><?php echo $row['comentario']?></td>
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
				tr.append($("<td>"+data.categoria+"</td>"));
				tr.append($("<td>"+Intl.NumberFormat().format(data.precio)+"</td>"));
				tr.append($("<td>"+data.comentario+"</td>"));
				tr.append($('<td data-id="'+data.id+'"><small class="label label-info">Editar</small> <small class="label label-danger">Eliminar</small></td>'));
				$("#table-body").append(tr);
			});
		});
	</script>
<?php
	include_once 'footer.php';
?>