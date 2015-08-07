var comentando = null;

$(function(){
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
				 	subTotal = parseFloat(totalContainer.find(".sub-total").attr('data-subtotal')) - parseFloat(jEl.find('.precio').html());
				 	
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
			 	subTotal = parseFloat(totalContainer.find(".sub-total").attr('data-subtotal')) + parseFloat(jEl.find('.precio').html());
			 	
			 	totalContainer.find(".sub-total").attr('data-subtotal', subTotal);
			 	totalContainer.find(".sub-total").html(subTotal);

			 	totalContainer.find(".total").attr('data-total', subTotal);
			 	totalContainer.find(".total").html(subTotal);

			 	if(jEl.children().attr("data-pedido")==0){
			 		jEl.find(".botones").removeClass("hide");
			 	}
			 	jEl.find('.btn-eliminar').on('click touchstart', function(){
			 		jEl.closest('.listas-secundarias').find("[name=cuentaActivaRadio]").prop("checked", true).change();

			 		jEl.remove();
			 		
			 		var subTotal = parseFloat(totalContainer.find(".sub-total").attr('data-subtotal')) - parseFloat(jEl.find('.precio').html());
			 	
				 	totalContainer.find(".sub-total").attr('data-subtotal', subTotal);
				 	totalContainer.find(".sub-total").html(subTotal);

				 	totalContainer.find(".total").attr('data-total', subTotal);
				 	totalContainer.find(".total").html(subTotal);
			 	});

			 	var listaOrigen = $(evt.from).closest(".listas-secundarias"),
			 		listaDestino = $(evt.item).closest(".listas-secundarias");
			 	if(listaOrigen.attr("data-grupo") == listaDestino.attr("data-grupo")){
			 		listaDestino.find(".btn-eliminar-cuenta").addClass("hide");
			 		if(listaOrigen.find("li").length == 0 && listaOrigen.attr("data-id") == undefined){
			 			listaOrigen.find(".btn-eliminar-cuenta").removeClass("hide");
			 		}
			 	}
			},
			scroll: true
		 });
	};

	var pedirComanda = function(refresh, param, callback){
		var ulActivoGrupo	= $("#listasContainer>[data-active=1]>ul");
		var nombreSelec		= ulActivoGrupo.parent().attr("data-nombre");
		var grupo 			= ulActivoGrupo.parent().attr("data-grupo");
		
		if(ulActivoGrupo.length!=0){
			var cuentas = [];
			$.each($("[data-grupo='"+grupo+"']>ul"), function(k,v){
				var ulActivo = $(v);
				var parent = ulActivo.parent();
				var nombreCuenta 	= parent.attr("data-nombre"),
					comentario 		= parent.attr("data-comentario").replace(/<br \/>/g, "\n");
					id				= parent.attr("data-id");
				var cuenta = {
								nombre		: parent.attr("data-nombre"),
								grupo		: parent.attr("data-grupo"),
								comentario	: parent.attr("data-comentario").replace(/<br \/>/g, "\n"),
								id			: parent.attr("data-id"),
								productos	: [],
								selec		: nombreSelec == parent.attr("data-nombre")?1:0
							};
				$.each(ulActivo.find("li"), function(k,v){
					var producto = $(v).children();
					
					$(v).find(".botones").addClass("hide");
					cuenta.productos.push({
						id: producto.attr("data-id"),
						comentario: producto.attr("data-comentario").replace(/<br \/>/g, "\n"),
						area: producto.attr("data-area"),
						pedido: producto.attr("data-pedido")
					});
					producto.attr("data-pedido", 1);
				});
				cuentas.push(cuenta);
			});
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "pedirComanda.php",
				data: {
					'cuentas':cuentas
				},
				success: function(data){
					if(data.ok){
						if(refresh == true){
							window.location.href = '?cuenta='+data.selec+'&ids='+data.ids.toString()+'&ei='+data.errorImpresion;
						}else{
							callback(param);
						}
					}else if(data.pedirLogin){
						/**
						* TODO
						* Abrir panel de login y hacer petición ajax, después re-pedir la comanda (si fue logueado con éxito).
						*/
					}
				},
				error: function(e){
					console.log(e.message);
				}
			});
		}
	}

	$(document).on("click ", ".mesasActivasContainer > button", function(){
		var nombre = $(this).html();
		lista = $("[data-nombre='"+nombre+"']");
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
		$(v).attr("data-iter", $("[data-grupo='"+$(v).attr("data-grupo")+"']").length - 1);
		crearLista($(v).find(".simpleList")[0], $(v).attr('data-grupo'));
		var boton = $('<button type="button" class="btn btn-primary btn-lg" style="margin: 5px;" data-nombre-boton="'+nombre+'">'+nombre+'</button>');
		$(".mesasActivasContainer").append(boton);
		if($(v).attr('data-seleccionada')==0){
			boton.trigger('click');
		}
	});

	var clonarLista = function(nombre, grupo){
		if(nombre.length != 0){
			var otherList = $("#mainList").clone();
			otherList.attr("data-nombre", nombre);
			otherList.attr("data-grupo", grupo);
			otherList.attr("data-comentario", "");
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
		
		if(nombre && $("[data-nombre='"+nombre+"']").length == 0 ){
			var otherList = clonarLista(nombre, nombre);
			$("#mainList").parent().append(otherList);
			$("#alertNuevaCuenta").addClass("hide")
			$(this).closest('.modal').modal('hide');
		}else{
			$("#alertNuevaCuenta").removeClass("hide")
		}
	});

	$(".dividirCuenta").on("click ",function(){
		var lista = $("[data-active=1]");
		if(lista.length != 0){
			lista.attr("data-iter",parseFloat(lista.attr("data-iter"))+1);
			var nombre = lista.attr("data-nombre")+'-'+lista.attr("data-iter");
			var grupo = lista.attr("data-grupo");
			var otherList = clonarLista(nombre, grupo);
			lista.after(otherList);
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

	$(".pedirComanda").on('click ', function(){
		pedirComanda(true);		
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

	$(document).on("click ", ".btn-eliminar-cuenta", function(){
		var element = $(this);
		var cuenta = element.closest(".listas-secundarias");
		$("[data-nombre-boton='"+cuenta.attr("data-nombre")+"']").remove();
		cuenta.remove();
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
		$(this).closest('.modal').modal('hide');
		
	});

	$('#modalJuntarCuentas').on('hidden.bs.modal', function () {
		var container = $(this);
		container.find("[type=submit]").prop('disabled', true);
		container.find(".alert").addClass("hide");
		var activos = container.find(".active");
		$.each(activos, function(k,element){
			$(element).removeClass("active");
			$(element).children("input").prop("checked", false);
		});
	});

	$('#modalJuntarCuentas').find('input[type=radio]').on("change", function(){
		var container = $(this).closest('.modal-body');
		var checked = container.find(".active > input");
		if(checked.length==2){
			if(checked[0].id==checked[1].id){
				container.find("#seleccionarCuentasDistintas").removeClass("hide");
				container.parent().find("[type=submit]").prop('disabled', true);
			}else{
				container.find("#seleccionarCuentasDistintas").addClass("hide");
				container.parent().find("[type=submit]").prop('disabled', false);
			}
		}else{
			container.parent().find("[type=submit]").prop('disabled', true);
		}
	});

	$("#formJuntarCuentas").submit(function(e){
		e.preventDefault();
		$.ajax({
				type: "POST",
				dataType: "json",
				url: "juntarCuentas.php",
				data: $(this).serialize(),
				success: function(data){
					if(data.ok){
						window.location.href = '?cuenta='+data.selec;
					}
				},
				error: function(e){
					console.log(e.message);
				}
		});
	});
	
});
