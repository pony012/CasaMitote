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

		$(function() {

		  // NOTE: $.tablesorter.theme.bootstrap is ALREADY INCLUDED in the jquery.tablesorter.widgets.js
		  // file; it is included here to show how you can modify the default classes
		  $.tablesorter.themes.bootstrap = {
		    // these classes are added to the table. To see other table classes available,
		    // look here: http://getbootstrap.com/css/#tables
		    table        : 'table table-bordered table-striped',
		    caption      : 'caption',
		    // header class names
		    header       : 'bootstrap-header', // give the header a gradient background (theme.bootstrap_2.css)
		    sortNone     : '',
		    sortAsc      : '',
		    sortDesc     : '',
		    active       : '', // applied when column is sorted
		    hover        : '', // custom css required - a defined bootstrap style may not override other classes
		    // icon class names
		    icons        : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
		    iconSortNone : 'bootstrap-icon-unsorted', // class name added to icon when column is not sorted
		    iconSortAsc  : 'glyphicon glyphicon-chevron-up', // class name added to icon when column has ascending sort
		    iconSortDesc : 'glyphicon glyphicon-chevron-down', // class name added to icon when column has descending sort
		    filterRow    : '', // filter row class; use widgetOptions.filter_cssFilter for the input/select element
		    footerRow    : '',
		    footerCells  : '',
		    even         : '', // even row zebra striping
		    odd          : ''  // odd row zebra striping
		  };

		  // call the tablesorter plugin and apply the uitheme widget
		  $("table").tablesorter({
		    // this will apply the bootstrap theme if "uitheme" widget is included
		    // the widgetOptions.uitheme is no longer required to be set
		    theme : "bootstrap",

		    widthFixed: true,

		    headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

		    // widget code contained in the jquery.tablesorter.widgets.js file
		    // use the zebra stripe widget if you plan on hiding any rows (filter widget)
		    widgets : [ "uitheme", "filter", "zebra" ],

		    widgetOptions : {
		      // using the default zebra striping class name, so it actually isn't included in the theme variable above
		      // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
		      zebra : ["even", "odd"],

		      // reset filters button
		      //filter_reset : ".reset",

		      // extra css class name (string or array) added to the filter element (input or select)
		      filter_cssFilter: "form-control",

		      // set the uitheme widget to use the bootstrap theme class names
		      // this is no longer required, if theme is set
		      // ,uitheme : "bootstrap"
		      filter_searchFiltered : false,
		      filter_columnFilters: true,
		      //filter_placeholder: { search : 'Search...' },
		      //filter_saveFilters : true,
		      filter_reset: '.reset'
		    }
		  });

		});
	</script>
<?php
	include_once 'footer.php';
?>