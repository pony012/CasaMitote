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
								<li class="list-group-item">
									<div class="row">
										<div class="col-xs-6 nombre"><?php echo $row2['nombre']?></div>
										<div class="col-xs-3 precio"><?php echo $row2['precio']?></div>
										<div class="col-xs-3 botones hide"><button class="btn btn-xs btn-danger btn-eliminar"><span class="glyphicon glyphicon-remove"></span></button></div>
									</div>
								</li>
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
			<div id="listasContainer">
				<div class="listas-secundarias col-xs-12 hide" id="mainList" data-active="0">
					<h4 class="tituloCuenta"></h4>
					<ul class="list-group simpleList" style="border: 1px solid black; min-height: 30px;padding: 0px;">
					</ul>
					<div class="total-cuenta row">
						<div class="col-xs-6">
							<b>Total:</b>
						</div>
						<div class="col-xs-3">
							<p class="sub-total" data-subtotal="0">0</p>
						</div>
						<div class="col-xs-3">
							<p class="total" data-total="0">0</p>
						</div>
					</div>
				</div>
			</div>
			<div>
				<div class="text-center col-xs-4">
					<button class="btn btn-primary agregarCuenta" data-toggle="modal" data-target="#modalNuevaCuenta" >Agregar Cuenta</button>
				</div>
				<div class="text-center col-xs-4">
					<button class="btn btn-primary dividircuenta" >Dividir Cuenta</button>
				</div>
				<div class="text-center col-xs-4">
					<button class="btn btn-success cobrarCuenta" data-toggle="modal" data-target=".bs-example-modal-sm">Cobrar Cuenta</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		<form method="POST" action="">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel"></h4>
					</div>
					<div class="modal-body">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Producto</th>
									<th>Costo</th>
									<th>Subtotal</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody id="tabla-cuenta">
								
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3" class="sub-total text-right"></td>
									<td class="total"></td>
								</tr>
							</tfoot>
						</table>
						<div class="row">
							<div class="col-xs-12">
								<div class="form-group">
									<label class="radio-inline">
									  <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"> Efectivo
									</label>
									<label class="radio-inline">
									  <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"> Tarjeta
									</label>
								</div>

								<div class="form-group col-xs-6">
									<label for="monto">Monto</label>
									<input name="monto" id="monto" type="number" placeholder="Monto" class="form-control">
								</div>
								<div class="form-group">
									<h3>Sobra:</h3>
									<b id="sobra">0</b>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-success">Cobrar</button>
					</div>
					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="labelNuevaCuenta" id="modalNuevaCuenta">
		<form action="" id="crearCuenta">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="labelNuevaCuenta">Abrir Cuenta</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Nombre de la cuenta" id="nombreNuevaCuenta">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-success">Crear</button>
					</div>
					</div>
				</div>
			</div>
		</form>
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
		var crearLista = function(lista, grupo){
			Sortable.create(lista, { 
				group: {
					name: grupo,
					put: ["Grupo1", grupo]
				},
				sort: false,
				onEnd: function (/**Event*/evt) {
					var itemEl = evt.item;	// dragged HTMLElement
					var newList = $(itemEl).parent();
					evt.from;	// previous list
					
				},
				onAdd: function(evt){
					var itemEl = evt.item;	// dragged HTMLElement
					var jEl = $(itemEl);
					var subTotal = 0;
					var totalContainer = null;
					if($(evt.from).hasClass("simpleList")){
						totalContainer = $(evt.from).parent().find(".total-cuenta");
					 	subTotal = parseInt(totalContainer.find(".sub-total").attr('data-subtotal')) - parseInt(jEl.find('.precio').html());
					 	
					 	totalContainer.find(".sub-total").attr('data-subtotal', subTotal);
					 	totalContainer.find(".sub-total").html(subTotal);

					 	totalContainer.find(".total").attr('data-total', subTotal);
					 	totalContainer.find(".total").html(subTotal);
					}

				 	var ul = $(itemEl).parent();
				 	ul.find($(itemEl)).remove();
				 	ul.append(itemEl);
				 	
				 	totalContainer = ul.parent().find(".total-cuenta");
				 	subTotal = parseInt(totalContainer.find(".sub-total").attr('data-subtotal')) + parseInt(jEl.find('.precio').html());
				 	
				 	totalContainer.find(".sub-total").attr('data-subtotal', subTotal);
				 	totalContainer.find(".sub-total").html(subTotal);

				 	totalContainer.find(".total").attr('data-total', subTotal);
				 	totalContainer.find(".total").html(subTotal);

				 	jEl.find(".botones").removeClass("hide");
				 	jEl.find('.btn-eliminar').on('click touchend', function(){
				 		jEl.remove();
				 		var subTotal = parseInt(totalContainer.find(".sub-total").attr('data-subtotal')) - parseInt(jEl.find('.precio').html());
				 	
					 	totalContainer.find(".sub-total").attr('data-subtotal', subTotal);
					 	totalContainer.find(".sub-total").html(subTotal);

					 	totalContainer.find(".total").attr('data-total', subTotal);
					 	totalContainer.find(".total").html(subTotal);
				 	});
				},

				scroll: true
			 });
		}
		
		$(".cobrarCuenta").on('click touchend', function(){
			var tabla = $("#tabla-cuenta");
			tabla.empty();
			var ulActivo = $("#listasContainer>[data-active=1]>ul");
			$.each(ulActivo.find("li"), function(k,v){
				tabla.append("<tr><td>"+$(v).find(".nombre").html()+"</td><td>"+$(v).find(".precio").html()+"</td><td></td><td></td></tr>");
			});
			tabla.parent().find(".total").html(ulActivo.parent().find(".total").html());
			tabla.parent().find(".sub-total").html(ulActivo.parent().find(".sub-total").html());
		});

		$("#monto").on('input',function(){
			var totalCuenta = parseInt($(this).parent().parent().parent().parent().find(".total").html());
			var sobra = -(totalCuenta-parseInt($(this).val()));

			$("#sobra").html(sobra);

			if(sobra<0){
				$("#sobra").addClass('text-danger');
			}else{
				$("#sobra").removeClass('text-danger');
			}
		});

		$("#crearCuenta").submit(function(e){
			e.preventDefault();
			var nombre = $("#nombreNuevaCuenta").val();
			if(nombre.length != 0){
				var otherList = $("#mainList").clone();
				otherList.removeAttr("id");
				otherList.find(".tituloCuenta").html(nombre);
				otherList.attr("data-active","1");
				otherList.removeClass("hide");
				$("#mainList").parent().append(otherList);
				crearLista(otherList.find(".simpleList")[0], nombre);
			}
			$(this).parent().modal('hide');
		});
		$('#modalNuevaCuenta').on('shown.bs.modal', function () {
		    $("#nombreNuevaCuenta").focus();
		})
		$('#modalNuevaCuenta').on('hidden.bs.modal', function () {
		    $("#nombreNuevaCuenta").val('');
		})
		
	</script>
<?php
	include_once 'footer.php';
?>