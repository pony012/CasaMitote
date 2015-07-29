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

	$stmt = $baseMdl->driver->prepare("SELECT * FROM TiposProductos");
	
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
									<div class="row" data-id="<?php echo $row2['idProducto']?>" data-area="<?php echo $row['area']?>" data-pedido="0" data-comentario="">
										<div class="col-xs-6 nombre"><?php echo $row2['nombre']?></div>
										<div class="col-xs-3 precio"><?php echo $row2['precio']?></div>
										<div class="col-xs-3 botones hide">
											<button class="btn btn-xs btn-info btn-comentario" data-toggle="modal" data-target="#modalComentario"><span class="glyphicon glyphicon-pencil"></span></button>
											<button class="btn btn-xs btn-danger btn-eliminar"><span class="glyphicon glyphicon-remove"></span></button>
										</div>
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
			<div class="row">
				<div class="col-xs-12 mesasActivasContainer">
					
				</div>
			</div>
		</div>

		<div class="col-xs-6">
			<div id="listasContainer">
				<div class="listas-secundarias col-xs-12 hide" id="mainList" data-active="0">
					<h4 class="tituloCuenta" style="display: inline"></h4>
					<div class="radio-inline">
						<label>
							<input type="radio" name="cuentaActivaRadio"> Activa
						</label>
					</div>
					<div class="botones" style="display: inline; margin-left:15px;">
						<button class="btn btn-xs btn-info btn-comentario" data-cuenta="1" data-toggle="modal" data-target="#modalComentario"><span class="glyphicon glyphicon-pencil"></span></button>
						<button class="btn btn-xs btn-danger btn-eliminar"><span class="glyphicon glyphicon-remove"></span></button>
					</div>
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
<?php
	if(isset($_SESSION['user'])){
?>
				<div class="row" style="margin-bottom: 15px;">
					<div class="text-center col-xs-4">
						<button class="btn btn-primary agregarCuenta" data-toggle="modal" data-target="#modalNuevaCuenta" >Agregar Cuenta</button>
					</div>
					<div class="text-center col-xs-4">
						<button class="btn btn-primary dividirCuenta" >Dividir Cuenta</button>
					</div>
					<div class="text-center col-xs-4">
						<button class="btn btn-success cobrarCuenta">Cobrar Cuenta</button>
					</div>
				</div>
				<div class="row" style="margin-bottom: 15px;">
					<div class="text-center col-xs-4">
						<button class="btn btn-success pedirComanda">Pedir Comanda</button>
					</div>	
				</div>
<?php
	}
?>
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
								<div class="col-xs-6">
									<div class="form-group">
										<label class="radio-inline">
										  <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"> Efectivo
										</label>
										<label class="radio-inline">
										  <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"> Tarjeta
										</label>
									</div>
									<div class="form-group">
										<label for="monto">Monto</label>
										<input name="monto" id="monto" type="number" placeholder="Monto" class="form-control">
									</div>
								</div>
								<div class="col-xs-6">
									<div class="form-group">
										<label class="radio-inline">
										  <input type="radio" name="inlineRadioOptions2" id="inlineRadio3" value="option3"> Efectivo
										</label>
										<label class="radio-inline">
										  <input type="radio" name="inlineRadioOptions2" id="inlineRadio4" value="option4"> Tarjeta
										</label>
									</div>
									<div class="form-group">
										<label for="monto2">Monto</label>
										<input name="monto2" id="monto2" type="number" placeholder="Monto" class="form-control">
									</div>
								</div>	
								<div class="col-xs-12">
									<div class="form-group">
										<h3>Sobra:</h3>
										<b id="sobra">0</b>
									</div>
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
						<div class="alert alert-warning hide" id="alertNuevaCuenta" role="alert">Ese nombre ya existe :(</div>
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
	
	<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="labelComentario" id="modalComentario">
		<form action="" id="ingresarComentario">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="labelComentario"></h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<textarea class="form-control" name="comentario" id="comentario" placeholder="Comentario..."></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-success">Aplicar Comentario</butoton>
					</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<script>
		var comentando = null;

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
				 	ul.parent().find("[name=cuentaActivaRadio]").prop("checked", true).change();

				 	totalContainer = ul.parent().find(".total-cuenta");
				 	subTotal = parseInt(totalContainer.find(".sub-total").attr('data-subtotal')) + parseInt(jEl.find('.precio').html());
				 	
				 	totalContainer.find(".sub-total").attr('data-subtotal', subTotal);
				 	totalContainer.find(".sub-total").html(subTotal);

				 	totalContainer.find(".total").attr('data-total', subTotal);
				 	totalContainer.find(".total").html(subTotal);

				 	if(jEl.children().attr("data-pedido")==0){
				 		jEl.find(".botones").removeClass("hide");
				 	}
				 	jEl.find('.btn-eliminar').on('click touchend', function(){
				 		jEl.parent().parent().find("[name=cuentaActivaRadio]").prop("checked", true).change();

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
		
		var clonarLista = function(nombre, grupo){
			if(nombre.length != 0){
				var otherList = $("#mainList").clone();
				otherList.attr("data-nombre", nombre);
				otherList.attr("data-grupo", grupo);
				otherList.attr("data-iter", 0);
				otherList.removeAttr("id");
				otherList.find(".tituloCuenta").html(nombre);
				otherList.removeClass("hide");
				otherList.find("[name=cuentaActivaRadio]").prop("checked", true).change();
				$("[data-active=1]").attr("data-active",0);
				otherList.attr("data-active", "1");
				crearLista(otherList.find(".simpleList")[0], grupo);
				$(".mesasActivasContainer").append($('<button type="button" class="btn btn-primary btn-lg" style="margin: 5px;" data-nombre-boton="'+nombre+'">'+nombre+'</button>'));
				return otherList;
			}
		}

		$("#crearCuenta").submit(function(e){
			e.preventDefault();
			var nombre = $("#nombreNuevaCuenta").val();
			
			if(nombre && $("[data-nombre="+nombre+"]").length == 0 ){
				var otherList = clonarLista(nombre, nombre);
				$("#mainList").parent().append(otherList);
				$("#alertNuevaCuenta").addClass("hide")
				$(this).parent().modal('hide');
			}else{
				$("#alertNuevaCuenta").removeClass("hide")
			}
		});

		$(".dividirCuenta").on("click touchend",function(){
			var lista = $("[data-active=1]");
			if(lista.length != 0){
				lista.attr("data-iter",parseInt(lista.attr("data-iter"))+1);
				var nombre = lista.attr("data-nombre")+'-'+lista.attr("data-iter");
				var grupo = lista.attr("data-grupo");
				var otherList = clonarLista(nombre, grupo);
				lista.after(otherList);
			}
		});

		$(".cobrarCuenta").on('click touchend', function(){
			var tabla = $("#tabla-cuenta");
			tabla.empty();
			var ulActivo = $("#listasContainer>[data-active=1]>ul");
			if(ulActivo.length!=0){
				$(".bs-example-modal-sm").modal("show");
				$.each(ulActivo.find("li"), function(k,v){
					tabla.append("<tr><td>"+$(v).find(".nombre").html()+"</td><td>"+$(v).find(".precio").html()+"</td><td></td><td></td></tr>");
				});
				tabla.parent().find(".total").html(ulActivo.parent().find(".total").html());
				$("#sobra").html(-ulActivo.parent().find(".total").html());
				$("#sobra").addClass('text-danger');
				$("#monto").val('');
				$("#monto2").val('');
				tabla.parent().find(".sub-total").html(ulActivo.parent().find(".sub-total").html());
			}
		});

		$(".pedirComanda").on('click touchend', function(){
			var ulActivo 		= $("#listasContainer>[data-active=1]>ul");
			var parent 			= ulActivo.parent();
			var nombreCuenta 	= parent.attr("data-nombre"),
				grupo 			= parent.attr("data-grupo");
				comentario 		= parent.attr("data-comentario");
				id				= parent.attr("data-id");
			if(ulActivo.length!=0){
				/*
				* TODO
				*	Enviar petición ajax para pedir la comanda
				*/

				var cuenta = {
								nombre		: parent.attr("data-nombre"),
								grupo		: parent.attr("data-grupo"),
								comentario	: parent.attr("data-comentario"),
								id			: parent.attr("data-id"),
								productos	:[],
							};
				$.each(ulActivo.find("li"), function(k,v){
					var producto = $(v).children();
					if(producto.attr("data-pedido")==0){
						$(v).find(".botones").addClass("hide");
						producto.attr("data-pedido", 1);
						cuenta.productos.push({
							id: producto.attr("data-id"),
							comentario: producto.attr("data-comentario"),
							area: producto.attr("data-area")
						});
					}
					//tabla.append("<tr><td>"+$(v).find(".nombre").html()+"</td><td>"+$(v).find(".precio").html()+"</td><td></td><td></td></tr>");
				});
				$.ajax({
					type: "POST",
					dataType: "json",
					url: "pedirComanda.php",
					data: {
						'cuenta':cuenta
					},
					//contentType: "application/json; charset=utf-8",
					success: function(data){
					    console.log("Added");
					},
					error: function(e){
					    console.log(e.message);
					}
				});
				console.log(cuenta);
				//tabla.parent().find(".total").html(ulActivo.parent().find(".total").html());
			}
		});

		$("#monto, #monto2").on('input',function(){
			var totalCuenta = parseInt($(this).parent().parent().parent().parent().parent().find(".total").html());
			var monto1 = $("#monto").val() || 0,
				monto2 = $("#monto2").val() || 0;

			var sobra = -(totalCuenta-parseInt(monto1)-parseInt(monto2));

			$("#sobra").html(sobra);

			if(sobra<0){
				$("#sobra").addClass('text-danger');
			}else{
				$("#sobra").removeClass('text-danger');
			}
		});

		$('#modalNuevaCuenta').on('shown.bs.modal', function () {
		    $("#nombreNuevaCuenta").focus();
		});
		$('#modalNuevaCuenta').on('hidden.bs.modal', function () {
		    $("#nombreNuevaCuenta").val('');
		});

		$('#modalComentario').on('shown.bs.modal', function () {
		    $("#comentario").focus();
		});
		$('#modalComentario').on('hidden.bs.modal', function () {
		    $("#comentario").val('');
		    comentando = null;
		});

		$(document).on("change", "[name=cuentaActivaRadio]", function(){
			$("[data-active=1]").attr("data-active", "0");
			$(this).parent().parent().parent().attr("data-active", "1");
		});

		$(document).on("click touchend", ".mesasActivasContainer > button", function(){
			var nombre = $(this).html();
			lista = $("[data-nombre="+nombre);
			$(this).toggleClass("disabled");
			if(lista.attr("data-active")=="1"){
				lista.attr("data-active",0);
				lista.find("[name=cuentaActivaRadio]").prop("checked", false);
				if(lista.nextAll(":not(.hide)").length){
					$(lista.nextAll(":not(.hide)")[0]).find("[name=cuentaActivaRadio]").prop("checked", true).change();
				}else if(lista.prevAll(":not(.hide)").length){
					$(lista.prevAll(":not(.hide)")[0]).find("[name=cuentaActivaRadio]").prop("checked", true).change();
				}
			}else if(lista.hasClass("hide")){
				lista.find("[name=cuentaActivaRadio]").prop("checked", true).change();
			}
			lista.toggleClass("hide");
		});

		$(document).on("click touchend", ".btn-comentario", function(){
			var element = $(this);
			comentando = element;
			if(element.attr("data-cuenta") == 1){
				var nombreCuenta = element.parent().parent().find('.tituloCuenta').html();
				$("#labelComentario").html(nombreCuenta);
			}else{
				var nombreProducto = element.parent().parent().find('.nombre').html();
				var nombreCuenta = element.closest('[data-nombre]').attr("data-nombre");
				$("#labelComentario").html(nombreCuenta +" | "+ nombreProducto);
			}
			$("#comentario").val(element.parent().parent().attr("data-comentario"));
		});

		$("#ingresarComentario").submit(function(e){
			e.preventDefault();
			comentando.parent().parent().attr("data-comentario", $("#comentario").val());
			if($("#comentario").val().length > 0){
				comentando.removeClass("btn-info");
				comentando.addClass("btn-success");
			}else{
				comentando.addClass("btn-info");
				comentando.removeClass("btn-success");
			}
			$(this).parent().modal('hide');
			
		});
	</script>
<?php
	include_once 'footer.php';
?>