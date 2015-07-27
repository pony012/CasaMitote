<?php
	include_once 'functions.php';
	include_once 'header.php';	
?>
	<!-- tablesorter plugin -->
	<script src="bower_components/jquery.tablesorter/dist/js/jquery.tablesorter.combined.js"></script>

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
				<table>
					<thead>
						<tr>
							<th>Nombre</th>
							<th class="filter-select filter-exact" data-placeholder="Categoria">Categoría</th>
							<th>Precio ($)</th>
							<th>Comentario</th>
							<th>Opciones</th>
						</tr>
					</thead>
					<tbody id="table-body">
		<?php
			$baseMdl = new BaseMdl();

			$stmt = $baseMdl->driver->prepare("SELECT T.nombre AS categoria, T.idTipoProducto, P.nombre, P.comentario, P.precio, P.idProducto AS id FROM 
												TiposProductos AS T
												RIGHT JOIN Productos AS P
												ON T.idTipoProducto = P.idTipoProducto
												WHERE P.activo = 1");
			
			if (!$stmt->execute()) {
				
			}else{
				$result = $stmt->get_result();
				if($result->field_count > 0){
					while($row = $result->fetch_array(MYSQLI_ASSOC)){
		?>
						<tr>
							<td><?php echo $row['nombre']?></td>
							<td data-idTipo="<?php echo $row['idTipoProducto'];?>"><?php echo $row['categoria']?></td>
							<td><?php echo number_format($row['precio'])?></td>
							<td><?php echo $row['comentario']?></td>
							<td data-id="<?php echo $row['id']?>">
								<button class="label label-info editar" data-toggle="modal" data-target="#modalEditarProducto">Editar</button>
								<button class="label label-danger eliminar" data-toggle="modal" data-target="#modalEliminarProducto">Eliminar</button>
							</td>
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
	<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="labelNuevaCuenta" id="modalEditarProducto">
		<form action="editProducto2.php" id="formEditar" method="POST">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="labelNuevaCuenta">Editar Producto</h4>
					</div>
					<div class="modal-body">
						<fieldset>
							<input type="hidden" name="idModal" id="idModal">
							<div class="form-group">
								<label for="nombreModal">Nombre</label>
								<input type="text" class="form-control" placeholder="Nombre" name="nombreModal" id="nombreModal">
							</div>
							<div class="form-group">
								<label for="precioModal">Precio</label>
								<input type="text" class="form-control" placeholder="Precio" name="precioModal" id="precioModal">
							</div>
							<div class="form-group">
								<label for="categoriaModal">Categoria</label>
								<select class="form-control" name="categoriaModal" id="categoriaModal">
									<option value="">Selecciona una categoria</option>
			<?php
				$baseMdl = new BaseMdl();

				$stmt = $baseMdl->driver->prepare("SELECT nombre, area, idTipoProducto as id FROM TiposProductos");
				
				if (!$stmt->execute()) {
					
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
								<label for="comentarioModal">Comentario</label>
								<textarea class="form-control" name="comentarioModal" id="comentarioModal" placeholder="Opcional..."></textarea>
							</div>
						</fieldset>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-success">Editar</button>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="labelNuevaCuenta" id="modalEliminarProducto">
		<form action="deleteProducto2.php" id="formEliminar" method="POST">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="labelNuevaCuenta">Eliminar Producto</h4>
					</div>
					<div class="modal-body">
						<fieldset>
							<input type="hidden" name="idEliminar" id="idEliminar">
							<div class="form-group">
								<label for="nombreEliminar">Nombre</label>
								<input type="text" class="form-control" placeholder="Nombre" name="nombreEliminar" id="nombreEliminar" disabled="disabled">
							</div>
							<div class="form-group">
								<label for="precioEliminar">Precio</label>
								<input type="text" class="form-control" placeholder="Precio" name="precioEliminar" id="precioEliminar" disabled="disabled">
							</div>
							<div class="form-group">
								<label for="categoriaEliminar">Categoria</label>
								<select class="form-control" name="categoriaEliminar" id="categoriaEliminar" disabled="disabled">
									<option value="">Selecciona una categoria</option>
			<?php
				$baseMdl = new BaseMdl();

				$stmt = $baseMdl->driver->prepare("SELECT nombre, area, idTipoProducto as id FROM TiposProductos");
				
				if (!$stmt->execute()) {
					
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
								<label for="comentarioEliminar">Comentario</label>
								<textarea class="form-control" name="comentarioEliminar" id="comentarioEliminar" placeholder="Opcional..." disabled="disabled"></textarea>
							</div>
						</fieldset>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-danger">Eliminar</button>
					</div>
				</div>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		$(function(){
			submitForm($("#form"), function(data){
				var tr = $('<tr>');
				tr.append($("<td>"+data.nombre+"</td>"));
				tr.append($("<td data-idTipo="+data.idCategoria+">"+data.categoria+"</td>"));
				tr.append($("<td>"+Intl.NumberFormat().format(data.precio)+"</td>"));
				tr.append($("<td>"+data.comentario+"</td>"));
				tr.append($('<td data-id="'+data.id+'"><button class="label label-info editar" data-toggle="modal" data-target="#modalEditarProducto">Editar</button> <button class="label label-danger eliminar" data-toggle="modal" data-target="#modalEliminarProducto">Eliminar</button></td>'));
				$("#table-body").append(tr);
			});

			$(document).on('click touchend', ".editar", function(){
				var tr = $(this).parent().parent();
				$("#idModal").val($(this).parent().attr("data-id"));
				$("#nombreModal").val(tr.children()[0].innerHTML);
				$("#precioModal").val(tr.children()[2].innerHTML);
				$("#categoriaModal").val($(tr.children()[1]).attr("data-idTipo"));
				$("#comentarioModal").val(tr.children()[3].innerHTML.replace(/<br>/g, "\n"));
			});

			$(document).on('click touchend', ".eliminar", function(){
				var tr = $(this).parent().parent();
				$("#idEliminar").val($(this).parent().attr("data-id"));
				$("#nombreEliminar").val(tr.children()[0].innerHTML);
				$("#precioEliminar").val(tr.children()[2].innerHTML);
				$("#categoriaEliminar").val($(tr.children()[1]).attr("data-idTipo"));
				$("#comentarioEliminar").val(tr.children()[3].innerHTML.replace(/<br>/g, "\n"));
			});

			submitForm($("#formEditar"), function(data){
				var tr = $("[data-id="+data.id+"]").parent();
				tr.empty();
				tr.append($("<td>"+data.nombre+"</td>"));
				tr.append($("<td data-idTipo="+data.idCategoria+">"+data.categoria+"</td>"));
				tr.append($("<td>"+Intl.NumberFormat().format(data.precio)+"</td>"));
				tr.append($("<td>"+data.comentario+"</td>"));
				tr.append($('<td data-id="'+data.id+'"><button class="label label-info editar" data-toggle="modal" data-target="#modalEditarProducto">Editar</button> <button class="label label-danger eliminar" data-toggle="modal" data-target="#modalEliminarProducto">Eliminar</button></td>'));
				$("#modalEditarProducto").modal("hide");
				//$("#table-body").append(tr);
			});

			submitForm($("#formEliminar"), function(data){
				var tr = $("[data-id="+data.id+"]").parent();
				tr.remove();
				$("#modalEliminarProducto").modal("hide");
				//$("#table-body").append(tr);
			});
		});

		$(function() {
		  
		  $.tablesorter.themes.bootstrap = {
		    
		    table        : 'table table-bordered table-striped',
		    caption      : 'caption',
		    
		    header       : 'bootstrap-header', 
		    sortNone     : '',
		    sortAsc      : '',
		    sortDesc     : '',
		    active       : '', 
		    hover        : '', 
		    
		    icons        : '', 
		    iconSortNone : 'bootstrap-icon-unsorted', 
		    iconSortAsc  : 'glyphicon glyphicon-chevron-up', 
		    iconSortDesc : 'glyphicon glyphicon-chevron-down', 
		  };

		  $("table").tablesorter({
		    theme : "bootstrap",

		    widthFixed: true,

		    headerTemplate : '{content} {icon}',

		    widgets : [ "uitheme", "filter", "zebra" ],

		    widgetOptions : {
		      zebra : ["even", "odd"],
		      filter_cssFilter: "form-control",
		      filter_searchFiltered : false,
		      filter_columnFilters: true,
		      filter_reset: '.reset'
		    }
		  });

		});
	</script>
<?php
	include_once 'footer.php';
?>