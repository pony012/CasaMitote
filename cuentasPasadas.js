var comentando = null;

$(function(){
	$(document).on("click ", ".mesasActivasContainer > button", function(){
		var nombre = $(this).attr('data-nombre-boton');
		var idCuenta = $(this).attr('data-id');
		lista = $("[data-nombre='"+nombre+"'][data-id="+idCuenta+"]");
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

	$.each($('.listas-secundarias:not(#mainList)'), function(k, v){
		var nombre = $(v).attr("data-nombre");
		var hora = $(v).attr("data-tiempo").split(" ")[1];
		var boton = $('<button type="button" class="btn btn-primary btn-lg" style="margin: 5px;" data-id="'+$(v).attr("data-id")+'" data-nombre-boton="'+nombre+'">'+nombre+'<br>'+hora+'</button>');
		$(".mesasActivasContainer").append(boton);
		if($(v).attr('data-seleccionada')==0){
			boton.trigger('click');
		}
	});

	$(".pedirCuenta").on('click ', function(){
		var tabla = $("#tabla-pedir-cuenta");
		tabla.empty();
		var ulActivo = $("#listasContainer>[data-active=1]>ul");
		if(ulActivo.closest('.listas-secundarias').attr("data-id") == undefined){
			
		}else{
			if(ulActivo.length!=0){
				var modal = $("#modalPedirCuenta");
				modal.modal("show");
				$('#idPedirCuenta').val(ulActivo.closest('.listas-secundarias').attr("data-id"));
				modal.find('.modal-title').html(ulActivo.closest('.listas-secundarias').attr("data-nombre"));
				$.each(ulActivo.find("li"), function(k,v){
					tabla.append("<tr><td>"+$(v).find(".nombre").html()+"</td><td>"+$(v).find(".precio").html()+"</td><td></td><td></td></tr>");
				});
				tabla.parent().find(".total").html(ulActivo.parent().find(".total").html());
				tabla.parent().find(".sub-total").html(ulActivo.parent().find(".sub-total").html());
			}
		}
	});

	$("#pedirCuentaForm").submit(function(e){
		e.preventDefault();
		var cuentaActiva = $("#listasContainer>[data-active=1]");

		pedirComanda(false, this, function(el){
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "pedirCuenta.php",
				data: $(el).serialize(),
				success: function(data){
					if(data.ok){
						$("#modalPedirCuenta").modal("hide");
						if(data.errorImpresion){
							$("#errorImpresion").removeClass('hide');
							$("#alert-container").append($('<div class="col-xs-4 col-xs-offset-4 alert alert-danger text-center" role="alert">'
																+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
																+'<p>Hubo un problema al pedir la cuenta <b>'+$("#listasContainer>[data-active=1]").attr("data-nombre")+'</b></p>'
															+'</div>'));
						}else{
							$("#alert-container").append($('<div class="col-xs-4 col-xs-offset-4 alert alert-success text-center" role="alert">'
																+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
																+'<p>Se pidió la cuenta <b>'+$("#listasContainer>[data-active=1]").attr("data-nombre")+'</b></p>'
															+'</div>'));
						}
						window.location.href = '#';
					}else if(data.pedirLogin){
						
					}
				},
				error: function(e){
					console.log(e.message);
				}
			})
		});
	});

	$(".cobrarCuenta").on('click ', function(){
		var tabla = $("#tabla-cuenta");
		tabla.empty();
		var ulActivo = $("#listasContainer>[data-active=1]>ul");
		if(ulActivo.closest('.listas-secundarias').attr("data-id") == undefined){
			
		}else{
			if(ulActivo.length!=0){
				var modal = $("#modalCobrarCuenta");
				modal.modal("show");
				$('#idCobrarCuenta').val(ulActivo.closest('.listas-secundarias').attr("data-id"));
				modal.find('.modal-title').html(ulActivo.closest('.listas-secundarias').attr("data-nombre"));
				$.each(ulActivo.find("li"), function(k,v){
					tabla.append("<tr><td>"+$(v).find(".nombre").html()+"</td><td>"+$(v).find(".precio").html()+"</td><td></td><td></td></tr>");
				});
				tabla.parent().find(".total").html(ulActivo.parent().find(".total").html());
				$("#sobra").html(-ulActivo.parent().find(".total").html());
				$("#sobra").addClass('text-danger');
				$("#monto1").val('');
				$("#monto2").val('');
				tabla.parent().find(".sub-total").html(ulActivo.parent().find(".sub-total").html());
			}
		}
	});

	$("#cobrarCuentaForm").submit(function(e){
		e.preventDefault();
		var cuentaActiva = $("#listasContainer>[data-active=1]");
		var pedir = true;
		if(cuentaActiva.find('[data-pedido=0]').length!=0){
			$("#alertProductosSinPedir").removeClass("hide");
			pedir = false;
		}else{
			$("#alertProductosSinPedir").addClass("hide");
		}

		if(parseFloat($("#sobra").html()) < 0){
			$("#alertCantidadInsuficiente").removeClass("hide");
			pedir = false;
		}else{
			$("#alertCantidadInsuficiente").addClass("hide");
		}

		if(pedir && cuentaActiva.attr("data-id") != '' && cuentaActiva.attr("data-id") != undefined){
			pedirComanda(false, this, function(el){
				$.ajax({
					type: "POST",
					dataType: "json",
					url: "cobrarCuenta.php",
					data: $(el).serialize(),
					success: function(data){
						if(data.ok){
							window.location.href = '?'+'&ei='+data.errorImpresion;
						}else if(data.pedirLogin){
							
						}
					},
					error: function(e){
						console.log(e.message);
					}
				})
			});
		}
		
	});

	$("#monto1, #monto2").on('input',function(){
		var totalCuenta = parseFloat($(this).closest('.modal-body').find(".total").html());
		var monto1 = $("#monto1").val() || 0,
			monto2 = $("#monto2").val() || 0;

		var sobra = -(totalCuenta-parseFloat(monto1)-parseFloat(monto2));

		$("#sobra").html(sobra);

		if(sobra<0){
			$("#sobra").addClass('text-danger');
		}else{
			$("#sobra").removeClass('text-danger');
		}
	});

	$('#modalComentario').on('shown.bs.modal', function () {
		$("#comentario").focus();
	});
	$('#modalComentario').on('hidden.bs.modal', function () {
		$("#comentario").val('');
		comentando = null;
	});

	$('#modalCobrarCuenta').on('shown.bs.modal', function () {
		
	});
	$('#modalCobrarCuenta').on('hidden.bs.modal', function () {
		$("#alertProductosSinPedir").addClass("hide");
		$("#alertCantidadInsuficiente").addClass("hide");
	});

	$(document).on("change", "[name=cuentaActivaRadio]", function(){
		$("[data-active=1]").attr("data-active", "0");
		$(this).closest('.listas-secundarias').attr("data-active", "1");
	});

	$(document).on("click touchstart", ".btn-comentario", function(){
		var element = $(this);
		comentando = element;
		if(element.attr("data-cuenta") == 1){
			var nombreCuenta = element.closest('.listas-secundarias').find('.tituloCuenta').html();
			$("#labelComentario").html(nombreCuenta);
		}else{
			var nombreProducto = element.closest('li').find('.nombre').html();
			var nombreCuenta = element.closest('[data-nombre]').attr("data-nombre");
			$("#labelComentario").html(nombreCuenta +" | "+ nombreProducto);
		}
		$("#comentario").val(element.closest('[data-comentario]').attr("data-comentario").replace(/<br \/>/g, "\n"));
		$("#modalComentario").modal('show');
	});

	$("#formLogin").submit(function(e){
		e.preventDefault();
		var modal = $(this).closest(".modal");
		var accion = modal.attr("data-accion");
		if(accion=="eliminarProducto"){
			var datos = $(this).serialize() + '&' + $.param({'idProducto':modal.attr("data-idProducto"),'idCuenta':modal.attr("data-idCuenta")});
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "eliminarProductoCuenta.php",
				data: datos,
				success: function(data){
					if(data.ok){
						window.location.href = '?cuenta='+data.selec;
					}else{
						var alertContainer = modal.find(".alert");
						if(data.error.code == 5 || data.error.code == 6){
							alertContainer.parent().removeClass("hide");
							alertContainer.html("No se pudo acceder");
							//No se pudo acceder
						}else if(data.error.code == 7){
							//Sin permisos suficientes
							alertContainer.parent().removeClass("hide");
							alertContainer.html("Esa cuenta no tiene permisos suficientes");
						}else{
							//Otro error
							alertContainer.parent().removeClass("hide");
							alertContainer.html("Hubo un error");
						}
					}
				},
				error: function(e){
					console.log(e.message);
				}
			});
		}
	});

	$('#modalLogin').on('hidden.bs.modal', function () {
		var container = $(this);
		container.attr("accion",'');
		container.attr("idProducto",'');
		container.attr("idCuenta",'');
		container.find("input").val('');
		container.find(".alert").parent().addClass("hide");
	});
	
});
