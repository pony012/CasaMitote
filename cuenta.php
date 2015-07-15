<?php
	include_once 'header.php';
?>
<style type="text/css">
	a.panel-handle{
	display: block;
	width: 100%;
	}
</style>
<script src="bower_components/Sortable/Sortable.min.js"></script>

	<div class="row">
		<!--
		<div class="col-md-4">
			<h2>Heading</h2>
			<pre><?php print_r(BaseCtrl::getUser(9810,"testRoot")); ?></pre>
			<p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
		</div>
		<div class="col-md-4">
			<h2>Heading</h2>
			<pre><?php print_r(BaseCtrl::getUser(1000,"testGerente")); ?></pre>
			<p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
		 	</div>
		<div class="col-md-4">
			<h2>Heading</h2>
			<pre><?php print_r(BaseCtrl::getUser(1000,"testRoot")); ?></pre>
			<p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
		</div>
		-->
	</div>

	<hr>

	<div class="row">
		<div class="col-xs-6">
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
<?php
	$baseMdl = new BaseMdl();

	$stmt = $baseMdl->driver->prepare("SELECT nombre, idTipoProducto FROM TiposProductos");
	
	if (!$stmt->execute()) {
	}else{
		$result = $stmt->get_result();
		if($result->field_count > 0){
			$i = 0;
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
?>
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="heading<?php echo $i?>">
						<h4 class="panel-title">
							<a role="button" class="panel-handle" data-toggle="collapse" href="#collapse<?php echo $i ?>" aria-expanded="true" aria-controls="collapse<?php echo $i ?>">
							<?php echo $row['nombre']?>
							</a>
						</h4>
					</div>
					<div id="collapse<?php echo $i ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading<?php echo $i?>">
						<ul id="simpleListSrc<?php echo $i?>" class="list-group src-list">
<?php 
				$baseMdl2 = new BaseMdl();

				$stmt2 = $baseMdl2->driver->prepare("SELECT * FROM Productos WHERE idTipoProducto = {$row['idTipoProducto']} AND activo = 1");
				
				if (!$stmt2->execute()) {
				}else{
					$result2 = $stmt2->get_result();
					if($result2->field_count > 0){
						while($row2 = $result2->fetch_array(MYSQLI_ASSOC)){
?>
								<li class="list-group-item"><?php echo $row2['nombre']?></li>
<?php
						}
					}
				}
?>
						</ul>
					</div>
				</div>
<?php
				$i++;
			}
		}
	}
?>
			</div>
		</div>

		<div class="col-xs-6">
  			<div class="listas-secundarias col-xs-12" id="listasContainer">
  				<input type="text" />
      			<ul id="simpleList2" class="list-group" style="border: 1px solid black; min-height: 30px;">
				    
				</ul>
			</div>
			<div style="text-align: center;">
				<button class="btn btn-primary" id="agregarCuenta">Agregar Cuenta</button>
			</div>
  		</div>
	</div>
	<script>
		$.each($('.src-list'), function(k,v){
			Sortable.create(v, {
				group: {
					name: "Grupo1", 
					pull: 'clone', 
					put: false
				},
				sort: false
			});
		});
		
		Sortable.create(simpleList2, { 
			group: {
				name: "Grupo1", 
				pull: true, 
				put: true
			},
			sort: false,
			onEnd: function (/**Event*/evt) {
				var itemEl = evt.item;	// dragged HTMLElement
				var newList = $(itemEl).parent();
				evt.from;	// previous list
				// + indexes from onEnd
			},
			onAdd: function(evt){
				itemEl = evt.item;	// dragged HTMLElement
				evt.from;	// previous list
				 	parent = $(itemEl).parent();
				 	parent.find($(itemEl)).remove();
				 	parent.append(itemEl);

			},
			scroll: true
		 });
	</script>
<?php
	include_once 'footer.php';
?>