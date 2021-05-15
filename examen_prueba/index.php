<!DOCTYPE html>
<html>
<head>
	<title>Prestamos</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="css/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/styles.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/themes/default.min.css">
	<link rel="stylesheet" type="text/css" href="css/alertify.min.css">

</head>
<body onload="revisar_alertas();">

<div id="alerta_container" class="alerta_container_normal">
	<label class="titulo_alerta">¡ALERTA!</label><br>
	<label class="lbl_sub_t">Clientes con plazo vencido</label><br>
	<div id="alerta_vencido"></div>
	<center><button class="btn btn2" style="margin-bottom: 5px;margin-top: 5px;" onclick="cerrar_alerta();">Cerrar</button></center>
</div>

	<div id="container">
		<table border="1" class="fondo_cuerpos">
			<tr class="fondo_titulos" style="position: relative; text-align: center;">
				<th colspan="4"><label style="font-weight: bold;">REGISTRO DE PRESTAMOS</label></th>
			</tr>
			<tr>
				<td><label style="margin-left: 10px;">Cliente</label></td>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td style="width: 15%;">
					<input class="form-control" type="text" name="" id="inp_nombre" style="margin-left: 10px; width: 200px;">
				</td>
				<td style="width: 15%;">
					<button class="btn btn1" id="btn_buscar" onclick="Buscar_cliente();">Buscar</button>
				</td>
				<td colspan="2" style="position: relative; text-align: right; width: 25%;">
					<button class="btn btn2" id="btn_agregar_prestamo" data-toggle="modal" data-target="#myModal" style="margin-right: 5px;">Agregar Préstamo</button>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<center>
						<table class="table table-bordered border-dark table-hover" style="position: relative; top: 10px; text-align: center; display: none;" id="table">
						    <tr style="background-color: #E8E8E8">
						        <th style="display: none;">ID_prestamo</th>
						        <th style="display: none;">ID_cliente</th>
						        <th>CLIENTE</th>
						        <th>MONTO DEL PRESTAMO</th>
						        <th>FECHA PRESTAMO</th>
						        <th>PLAZO</th>
						        <th>ACCIONES</th>
						    </tr>
						</table>
					</center>
				</td>
			</tr>
		</table>
	</div>

	<div id="footer_container">
		<label>© Copyright 2001-2021 caprepa.com - Todos los Derechos Reservados - Legal</label>
	</div>

	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap/popper.min.js"></script>
	<script src="js/bootstrap/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/functions.js"></script>
	<script type="text/javascript" src="js/alertify.min.js"></script>
</body>
</html>

<!-------------------------------------------------------- Contenerdor de modal's -------------------------------------------------------->
<div class="container_modal">
	<!----------------------------------------------------- Modal prestamos -->
	<div class="modal fade" id="myModal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<!-- Modal Header -->
				<div class="row fondo_titulos">
					<div class="col-lg-12">
						<label style="font-weight: bold; margin-top: 5px;margin-left: 5px;">AGREGAR UN PRESTAMO</label>
					</div>
				</div>

				<div class="row fondo_cuerpos">
					<div class="col-lg-1"></div>
					<div class="col-lg-4" id="selector_cliente">
						<label style="text-align: right; margin-top: 10px;">Cliente</label>
						<select id="sel_cat_cliente" class="form-control" style="margin-bottom: 5px;">
							<option value="0">Seleccione un Cliente</option>
							<?php
							require_once('php/config.php');

							$query = $dbh->prepare("SELECT IdCliente,Nombre FROM sandbox.catalogocliente ORDER BY Nombre;");
							$query->execute();
							$data = $query->fetchAll();

							foreach ($data as $valores) {
								echo '<option value="'.$valores["IdCliente"].'">'.$valores["Nombre"].'</option>';
							} 
							 ?>
						</select>
					</div>
					<div class="col-lg-2"></div>
					<div class="col-lg-4">
						<label style="text-align: right; margin-top: 10px;">Monto</label>
						<select id="sel_cat_monto" class="form-control" style="margin-bottom: 5px;">
							<option value="0">Seleccione un Monto</option>
							<?php
							require_once('php/config.php');

							$query = $dbh->prepare("SELECT Monto FROM sandbox.catalogomontosprestamo ORDER BY Monto;");
							$query->execute();
							$data = $query->fetchAll();

							foreach ($data as $valores) {
								echo '<option>$'.$valores["Monto"].'</option>';
							} 
							 ?>
						</select>
					</div>
					<div class="col-lg-1"></div>
				</div>

				<div class="row fondo_cuerpos">
					<div class="col-lg-1"></div>
					<div class="col-lg-4">
						<label style="text-align: right;">Plazo (en quincenas)</label>
						<select id="sel_cat_plazo" class="form-control" style="margin-bottom: 5px;">
							<option value="0">Seleccione un Plazo</option>
							<?php
							require_once('php/config.php');

							$query = $dbh->prepare("SELECT Plazo FROM sandbox.CatalogoPlazos ORDER BY Plazo;");
							$query->execute();
							$data = $query->fetchAll();

							foreach ($data as $valores) {
								echo '<option>'.$valores["Plazo"].'</option>';
							} 
							 ?>
						</select>
					</div>
					<div class="col-lg-6"></div>
					<div class="col-lg-1"></div>
				</div>

				<div class="row fondo_cuerpos">
					<div class="col-lg-4"></div>
					<div class="col-lg-1">
						<center>
							<button class="btn btn2" data-dismiss="modal" style="margin-top: 15px;" onclick="reseteo_combos();">Cancelar</button>
						</center>
					</div>
					<div class="col-lg-2"></div>
					<div class="col-lg-1">
						<center>
							<button class="btn btn1" id="btn_agregar" style="margin-top: 15px; margin-bottom: 20px;" onclick="agregar_prestamo();">Agregar</button>
						</center>
					</div>
					<div class="col-lg-4"></div>
				</div>
			</div>
		</div>
	</div>

	<!----------------------------------------------------- Modal Amortizacion -->
	<div id="miModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
		<!-- Contenido del modal -->
			<div class="modal-content">

				<!-- Modal Header -->
				<div class="row fondo_titulos">
					<div class="col-lg-8">
						<label style="font-weight: bold; margin-top: 5px;margin-left: 5px;">TABLA DE AMORTIZACION</label>
					</div>
					<div class="col-lg-4">
						<label id="lbl_status_prestamo" style="margin-top: 5px; position: relative; text-align: left;">status</label>
					</div>
				</div>

				<div class="row fondo_cuerpos">
					<div class="col-lg-1"></div>
					<div class="col-lg-4" id="selector_cliente">
						<label class="negritas" style="text-align: right; margin-top: 10px;">Cliente: </label>
						<label id="lbl_nom_cliente"></label>
					</div>
					<div class="col-lg-2"></div>
					<div class="col-lg-4">
						<label class="negritas" style="text-align: right; margin-top: 10px;">Fecha Limite: </label>
						<label id="lbl_fecha_limite"></label>
					</div>
					<div class="col-lg-1"></div>
				</div>

				<div class="row fondo_cuerpos">
					<div class="col-lg-1"></div>
					<div class="col-lg-4" id="selector_cliente">
						<label class="negritas" style="text-align: right; margin-top: 10px;">Monto Prestado: </label>
						<label id="lbl_monto_prestado"></label>
					</div>
					<div class="col-lg-2"></div>
					<div class="col-lg-4">
						<label class="negritas" style="text-align: right; margin-top: 10px;">Total a Pagar: </label>
						<label id="lbl_total_pagar"></label>
					</div>
					<div class="col-lg-1"></div>
				</div>

				<div class="row fondo_cuerpos">
					<div class="col-lg-1"></div>
					<div class="col-lg-4" id="selector_cliente">
						<label class="negritas" style="text-align: right; margin-top: 10px;">Plazo de pago: </label>
						<label id="lbl_plazo_pago"></label>
					</div>
					<div class="col-lg-2"></div>
					<div class="col-lg-4">
						<label class="negritas" style="text-align: right; margin-top: 10px;">Restante por Pagar: </label>
						<label id="lbl_restante_pagar"></label>
					</div>
					<div class="col-lg-1"></div>
				</div>

				<div class="row fondo_cuerpos">
					<div class="col-lg-1"></div>
					<div class="col-lg-10">
						<center>
							<table class="table table-bordered border-dark table-hover" style="position: relative; top: 10px; text-align: center;margin-bottom: 25px;" id="table-amortizacion">
							    <tr style="background-color: #E8E8E8">
							        <th>NO. PAGO</th>
							        <th>FOLIO DE PAGO</th>
							        <th>FECHA</th>
							        <th>INTERES</th>
							        <th>ABONO</th>
							    </tr>
							</table>
						</center>
					</div>
					<div class="col-lg-1"></div>
				</div>

				<div class="row fondo_cuerpos">
					<div class="col-lg-5"></div>
					<div class="col-lg-2" style="margin-bottom: 10px;">
						<button class="btn btn2" data-dismiss="modal">Cerrar</button>
					</div>
					<div class="col-lg-5"></div>
				</div>
			</div>
		</div>
	</div>
</div>

