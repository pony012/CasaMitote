<?php
	include_once 'header.php';
?>
<div id="alert-container">
	<div class="col-xs-4 col-xs-offset-4 alert alert-warning text-center <?php if(!(isset($_GET['ei']) && $_GET['ei'] == 1)){echo 'hide';}?>" id="errorImpresion" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<p>Hubo un problema con las impresoras</p>
	</div>
</div>

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
						while($producto = $result2->fetch_array(MYSQLI_ASSOC)){
?>
								<li class="list-group-item">
									<div class="row" data-id="<?php echo $producto['idProducto']?>" data-area="<?php echo $row['area']?>" data-pedido="0" data-comentario="">
										<div class="col-xs-6 nombre"><?php echo $producto['nombre']?></div>
										<div class="col-xs-3 precio"><?php echo $producto['precio']?></div>
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
<?php
	if(isset($_SESSION['user'])){
?>
			<div id="listasContainer">
<?php 
		$cuentaMdl = new BaseMdl();

		$cuentaSeleccionada = isset($_GET['cuenta'])?$_GET['cuenta']:NULL;
		$seleccionadas		= isset($_GET['ids'])?explode(',', $_GET['ids']):array();

		$stmt = $cuentaMdl->driver->prepare("SELECT * FROM Cuentas WHERE pagada = 0 AND activa = 1");
		$mesas = [];
		if (!$stmt->execute()) {
		}else{
			$result = $stmt->get_result();
			if($result->field_count > 0){
				$i = 0;
				while($cuenta = $result->fetch_array(MYSQLI_ASSOC)){
					$total = 0;
					$mesas[] = $cuenta;
?>
				<div class="listas-secundarias col-xs-12"
						data-seleccionada="<?php echo $cuentaSeleccionada==$cuenta['idCuenta']||in_array($cuenta['idCuenta'], $seleccionadas)?1:0; ?>"
						data-active="<?php echo $cuentaSeleccionada==$cuenta['idCuenta']?1:0;?>"
						data-nombre="<?php echo $cuenta['nombre'];?>" 
						data-grupo="<?php echo $cuenta['grupo'];?>"
						data-iter="0"
						data-comentario="<?php echo $cuenta['comentario'];?>"
						data-id="<?php echo $cuenta['idCuenta'];?>">
					<h4 class="tituloCuenta" style="display: inline"><?php echo $cuenta['nombre'];?></h4>
					<div class="radio-inline">
						<label>
							<input type="radio" name="cuentaActivaRadio" <?php if($cuentaSeleccionada==$cuenta['idCuenta']){echo 'checked="checked"';}?>> Activa
						</label>
					</div>
					<div class="botones" style="display: inline; margin-left:15px;">
						<button class="btn btn-xs <?php echo strlen($cuenta['comentario'])>0?'btn-success':'btn-info';?> btn-comentario" data-cuenta="1" data-toggle="modal" data-target="#modalComentario"><span class="glyphicon glyphicon-pencil"></span></button>
						<button class="btn btn-xs btn-danger btn-elimianr hide"><span class="glyphicon glyphicon-remove"></span></button>
					</div>
					<ul class="list-group simpleList" style="border: 1px solid black; min-height: 30px;padding: 0px;">
<?php 
					$baseMdl2 = new BaseMdl();

					$stmt2 = $baseMdl2->driver->prepare("SELECT P.idProducto, P.nombre, PC.comentario, P.precio, TP.area
															FROM ProductosCuenta AS PC
															LEFT JOIN Productos AS P ON P.idProducto = PC.idProducto
															LEFT JOIN TiposProductos AS TP ON TP.idTipoProducto = P.idTipoProducto
															WHERE idCuenta = {$cuenta['idCuenta']}");
					
					if (!$stmt2->execute()) {
					}else{
						$result2 = $stmt2->get_result();
						if($result2->field_count > 0){
							while($producto = $result2->fetch_array(MYSQLI_ASSOC)){
								$total += $producto['precio'];
?>
						<li class="list-group-item" draggable="false">
							<div class="row" 
									data-id="<?php echo $producto['idProducto']?>" 
									data-area="<?php echo $producto['area']?>" 
									data-pedido="1" 
									data-comentario="<?php echo $producto['comentario'];?>">
								<div class="col-xs-6 nombre"><?php echo $producto['nombre']?></div>
								<div class="col-xs-3 precio"><?php echo $producto['precio']?></div>
								<div class="col-xs-3 botones">
									<button class="btn btn-xs btn-info btn-comentario hide" data-toggle="modal" data-target="#modalComentario"><span class="glyphicon glyphicon-pencil"></span></button>
									<button class="btn btn-xs btn-danger btn-eliminar"><span class="glyphicon glyphicon-remove"></span></button>
								</div>
							</div>
						</li>
<?php
							}
						}
					}
					$stmt2->close();
?>
					</ul>
					<div class="total-cuenta row">
						<div class="col-xs-6">
							<b>Total:</b>
						</div>
						<div class="col-xs-3">
							<p class="sub-total" data-subtotal="<?php echo $total;?>"><?php echo $total;?></p>
						</div>
						<div class="col-xs-3">
							<p class="total" data-total="<?php echo $total;?>"><?php echo $total;?></p>
						</div>
					</div>
				</div>				
<?php
				}
			}
		}
?>
				<div class="listas-secundarias col-xs-12 hide" id="mainList" data-active="0">
					<h4 class="tituloCuenta" style="display: inline"></h4>
					<div class="radio-inline">
						<label>
							<input type="radio" name="cuentaActivaRadio"> Activa
						</label>
					</div>
					<div class="botones" style="display: inline; margin-left:15px;">
						<button class="btn btn-xs btn-info btn-comentario" data-cuenta="1" data-toggle="modal" data-target="#modalComentario"><span class="glyphicon glyphicon-pencil"></span></button>
						<button class="btn btn-xs btn-danger btn-eliminar-cuenta"><span class="glyphicon glyphicon-remove"></span></button>
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
				<div class="row" style="margin-bottom: 15px;">
					<div class="text-center col-xs-4">
						<button class="btn btn-primary agregarCuenta" data-toggle="modal" data-target="#modalNuevaCuenta" >Agregar Cuenta</button>
					</div>
					<div class="text-center col-xs-4">
						<button class="btn btn-primary dividirCuenta" >Dividir Cuenta</button>
					</div>
					<div class="text-center col-xs-4">
						<button class="btn btn-warning juntarCuentas" data-toggle="modal" data-target="#modalJuntarCuentas">Juntar Cuentas</button>
					</div>
				</div>
				<div class="row" style="margin-bottom: 15px;">
					<div class="text-center col-xs-4">
						<button class="btn btn-success pedirComanda">Imprimir Comanda</button>
					</div>	
					<div class="text-center col-xs-4">
						<button class="btn btn-success pedirCuenta">Imprimir Cuenta</button>
					</div>
					<div class="text-center col-xs-4">
						<button class="btn btn-success cobrarCuenta">Pagar Cuenta</button>
					</div>
				</div>
			</div>
<?php
	}
?>
		</div>
	</div>

	<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="modalPedirCuenta">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="POST" id="pedirCuentaForm">
					<input type="hidden" name="idCuenta" id="idPedirCuenta" value="">
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
								</tr>
							</thead>
							<tbody id="tabla-pedir-cuenta">
								
							</tbody>
							<tfoot>
								<tr>
									<td><b>Subtotal</b></td>
									<td class="sub-total"></td>
								</tr>
								<tr>
									<td><b>Total</b></td>
									<td class="total"></td>
								</tr>
							</tfoot>
						</table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-success">Pedir</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="modalCobrarCuenta">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="POST" id="cobrarCuentaForm">
					<input type="hidden" name="idCuenta" id="idCobrarCuenta" value="">
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
								</tr>
							</thead>
							<tbody id="tabla-cuenta">
								
							</tbody>
							<tfoot>
								<tr>
									<td><b>Subtotal</b></td>
									<td class="sub-total"></td>
								</tr>
								<tr>
									<td><b>Total</b></td>
									<td class="total"></td>
								</tr>
							</tfoot>
						</table>
						<div class="row">
							<div class="col-xs-12">
								<div class="col-xs-6">
									<div class="form-group">
										<label class="radio-inline">
										  <input type="radio" name="metodoPago1" id="inlineRadio1" value="efectivo" checked="checked"> Efectivo
										</label>
										<label class="radio-inline">
										  <input type="radio" name="metodoPago1" id="inlineRadio2" value="tarjeta"> Tarjeta
										</label>
									</div>
									<div class="form-group">
										<label for="monto1">Monto</label>
										<input name="monto1" id="monto1" type="number" step="any" min="0" placeholder="Monto" class="form-control">
									</div>
								</div>
								<div class="col-xs-6">
									<div class="form-group">
										<label class="radio-inline">
										  <input type="radio" name="metodoPago2" id="inlineRadio3" value="efectivo"> Efectivo
										</label>
										<label class="radio-inline">
										  <input type="radio" name="metodoPago2" id="inlineRadio4" value="tarjeta" checked="checked"> Tarjeta
										</label>
									</div>
									<div class="form-group">
										<label for="monto2">Monto</label>
										<input name="monto2" id="monto2" type="number" step="any" min="0" placeholder="Monto" class="form-control">
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
						<div class="alert alert-warning hide text-center" id="alertProductosSinPedir" role="alert">Aún hay productos por pedir.</div>
						<div class="alert alert-warning hide text-center" id="alertCantidadInsuficiente" role="alert">Cantidad de dinero insuficiente.</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-success">Cobrar</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="labelNuevaCuenta" id="modalNuevaCuenta">	
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<form action="" id="crearCuenta">
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
				</form>
			</div>
		</div>
	</div>
	
	<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="labelComentario" id="modalComentario">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<form action="" id="ingresarComentario">
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
						<button type="submit" class="btn btn-success">Aplicar Comentario</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="labelJuntar" id="modalJuntarCuentas">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<form id="formJuntarCuentas">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="labelJuntar">Juntar Cuentas</h4>
					</div>
					<div class="modal-body">
						<div class="col-xs-12 hide" id="seleccionarCuentasDistintas">
							<div class="text-center alert alert-warning">Debes seleccionar dos cuentas distintas</div>
						</div>
						<div data-toggle="buttons">
<?php
	foreach ($mesas as $cuenta) {
?>
							<label class="btn btn-primary btn-lg" style="margin: 5px;">
								<input type="radio" name="mesa1" value="<?php echo $cuenta['idCuenta'];?>" id="<?php echo $cuenta['idCuenta'];?>" autocomplete="off" 
										data-nombre-boton="<?php echo $cuenta['nombre'];?>"><?php echo $cuenta['nombre'];?>
							</label>	
<?php
	}
?>
						</div>
						<hr>
					<div data-toggle="buttons">
<?php
	foreach ($mesas as $cuenta) {
?>
							<label class="btn btn-primary btn-lg" style="margin: 5px;">
								<input type="radio" name="mesa2" value="<?php echo $cuenta['idCuenta'];?>" id="<?php echo $cuenta['idCuenta'];?>" autocomplete="off" 
										data-nombre-boton="<?php echo $cuenta['nombre'];?>"><?php echo $cuenta['nombre'];?>
							</label>	
<?php
	}
?>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-success">Juntar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="labelLogin" id="modalLogin">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<form id="formLogin">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="labelLogin">Inicia Sesión para realizar esa acción</h4>
					</div>
					<div class="modal-body">
						<div class="col-xs-12 hide">
							<div class="text-center alert alert-warning"></div>
						</div>
						<div class="form-group">
		                  <input type="text" placeholder="Nombre" class="form-control" name="user">
		                </div>
		                <div class="form-group">
		                  <input type="password" placeholder="Password" class="form-control" name="pass">
		                </div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-success">Entrar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script src="cuenta.js"></script>
<?php
	include_once 'footer.php';
?>
