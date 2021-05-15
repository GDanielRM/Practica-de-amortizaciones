function agregar_prestamo(){
	var cliente = $("#sel_cat_cliente option:selected").text();
	var Idcliente = $("#sel_cat_cliente option:selected").val();
	var monto = $("#sel_cat_monto option:selected").text();
	var plazo = $("#sel_cat_plazo option:selected").text();

	var monto_cortado = monto.substring(1, monto.length - 3);

	if (cliente == 'Seleccione un Cliente') {
		alerta('Por favor, seleccione un cliente');
	} else if (monto == 'Seleccione un Monto') {
		alerta('Por favor, selecciones un monto');
	} else if (plazo == 'Seleccione un Plazo') {
		alerta('Por favor, seleccione un plazo');
	} else {
		var parameters = {
			'opc' 		: 'registrar_prestamo',
			'cliente' 	: cliente,
			'idcliente' : Idcliente,
			'monto' 	: monto_cortado,
			'plazo' 	: plazo
		}

		$.ajax({
				cache: false,
				url: './php/config.php',
				type: 'POST',
				dataType: 'json',
				data: parameters,
				success: function(response) {
					if (response.estado = 1) {
						alertify.set('notifier','position', 'top-rigth');
 						alertify.success('Prestamo Registrado');

						reseteo_combos();
					} else {
						alert(response.mensaje);

					}
				},
				error:function(response) { 
					alert('Falló la respuesta del config.php; opc: registrar_prestamo');
				},
		});
	}
}

function Buscar_cliente(){
	var Cliente = $('#inp_nombre').val();

	if (Cliente == '') {
		alerta('Por favor, indique un nombre');
	} else {
		var parameters = {
			'opc' 		: 'buscar_prestamo_cliente',
			'cliente' 	: Cliente
		}

		$.ajax({
				cache: false,
				url: './php/config.php',
				type: 'POST',
				dataType: 'json',
				data: parameters,
				success: function(response) {
					if (response.estado == 1) {
						var filas = response.prestamos.length;

						$("tr").remove(".borrame");

						for(i = 0; i < filas; i++){
							var nuevafila = 
							"<tr class='borrame' style='background-color: #F7F5E6'>" + 
								"<td id='Id_Prestamo' style='display: none'>" + response.prestamos[i]['IdPrestamo'] + "</td>" + 
								"<td id='Id_Cliente' style='display: none'>" + response.prestamos[i]['IdCliente'] + "</td>" + 
								"<td>" + response.prestamos[i]['Cliente'] + "</td>" + 
								"<td>" + '$' +response.prestamos[i]['Monto'] + "</td>" + 
								"<td>" + response.prestamos[i]['Fecha'] + "</td>" + 
								"<td>" + response.prestamos[i]['Plazo'] + "</td>" + 
								"<td>" + '<button class="btn tamano_boton" id="Id_btn" data-toggle="modal" data-target="#miModal" onclick="Cargar_datos_pagos(this);"><i class="material-icons">reorder</i></button></td>"' + 
							'</tr>';

							$('#table').append(nuevafila);

							// Este bloque de codigo se usa para asignar los ID's de los los prestamos y clientes
							var Id_P = 'Id_Prestamo'+i;
							var Id_C = 'Id_Cliente'+i;
							var Id_B = Id_P+'-'+Id_C;
							$('#Id_Prestamo').attr('id', Id_P);
							$('#Id_Cliente').attr('id', Id_C);
							$('#Id_btn').attr('id', Id_B);
							
							$('#table').show();
						}
					} else {
						alerta(response.mensaje);
					}
				},
				error:function(response) { 
					alert('Falló la respuesta del config.php; opc: buscar_prestamo_cliente');
				},
		});
	}
}

var Cargar_datos_pagos = function(data){
	var ids = data.id.split('-');

	var idprestamo = $('#'+ids[0]).text();
	var idcliente = $('#'+ids[1]).text();

	var parameters = {
		'opc' 		: 'cargar_datos_pagos',
		'idprestamo' 	: idprestamo
	}

	$.ajax({
		cache: false,
		url: './php/config.php',
		type: 'POST',
		dataType: 'json',
		data: parameters,
		success: function(response) {
			// Llena los datos de la tabla
			if (response.estado_prestamo == 1) {

				var filas = response.dato_prestamo_tabla.length;

				$("tr").remove(".borrame_abonos");

				if (response.estado_datos_prestamo == 0) {
					var nuevafila = 
					"<tr class='borrame_abonos' style='background-color: #F7F5E6'>" + 
						"<td colspan='5' style='font-weight: bold;'>NO HAN REALIZADO ABONOS PARA ESTE PRESTAMO</td>" + 
					"</tr>";
				} else {
				
					var totalinteres = 0;
					var totalabonado = 0;
					for(i = 0; i < filas; i++){
						var nuevafila = 
						"<tr class='borrame_abonos' style='background-color: #F7F5E6'>" + 
							"<td>" + response.dato_prestamo_tabla[i]['rn'] + "</td>" + 
							"<td>" + response.dato_prestamo_tabla[i]['IdAbono'] + "</td>" + 
							"<td>" + response.dato_prestamo_tabla[i]['FechaAbono'] + "</td>" + 
							"<td>" + '$' + parseFloat(response.dato_prestamo_tabla[i]['interes']).toFixed(2) + "</td>" + 
							"<td>" + '$' + parseFloat(response.dato_prestamo_tabla[i]['MontoAbono']).toFixed(2) + "</td>" + 
						'</tr>';

						$('#table-amortizacion').append(nuevafila);
						
						totalinteres = totalinteres + parseFloat(response.dato_prestamo_tabla[i][2]);
						totalabonado = totalabonado + parseFloat(response.dato_prestamo_tabla[i][3]);
					}

					var nuevafila = 
					"<tr class='borrame_abonos negritas' style='background-color: #f2f2f2'>" + 
						"<td colspan='3'>TOTALES</td>" + 
						"<td>" + "$" + totalinteres.toFixed(2) + "</td>" + 
						"<td>" + "$" + totalabonado.toFixed(2) + "</td>" + 
					'</tr>';	
				}

				$('#table-amortizacion').append(nuevafila);
			} else {
				alert(response.mensaje_prestamo);	
			}

			// Llena los datos de la cabezera
			if (response.estado == 1) {

				$('#lbl_nom_cliente').text(response.dato_prestamo[0]['Cliente']);
				$('#lbl_monto_prestado').text('$'+response.dato_prestamo[0]['Monto']);
				$('#lbl_plazo_pago').text(response.dato_prestamo[0]['Plazo']+' Quincenas');
				$('#lbl_fecha_limite').text(response.dato_prestamo[0]['FechaLimite']);
				$('#lbl_total_pagar').text('$'+response.monto_pagar);

				if (totalabonado == undefined) {
					totalabonado = 0.00;
				}

				var restante_pagar = parseFloat(response.monto_pagar)-totalabonado;
				$('#lbl_restante_pagar').text("$"+restante_pagar);
			} else {
				alert(response.mensaje);
			}


			$('#lbl_status_prestamo').removeClass('liquidado');
			$('#lbl_status_prestamo').removeClass('vencido');
			$('#lbl_status_prestamo').text('');
			$('#lbl_fecha_limite').removeClass('vencido');

			if (restante_pagar == 0) {
				$('#lbl_status_prestamo').text('PRESTAMO LIQUIDADO');
				$('#lbl_status_prestamo').addClass('liquidado');
			} else {

				let fecha1 = new Date(response.dato_prestamo[0]['FechaLimite']);
				let fecha2 = new Date();
				let resta = fecha1.getTime() - fecha2.getTime();

				if (resta < 0) {
					$('#lbl_status_prestamo').text('PLAZO VENCIDO');
					$('#lbl_status_prestamo').addClass('vencido');
					$('#lbl_fecha_limite').addClass('vencido');
				}
			}
		},
		error:function(response) { 
			alert('Falló la respuesta del config.php; opc: cargar_datos_pagos');
		},
	});

}

function revisar_alertas(){
	setTimeout(function(){
		var parameters = {
			'opc'	: 'alertas'
		}

		$.ajax({
			cache: false,
			url: './php/config.php',
			type: 'POST',
			dataType: 'json',
			data: parameters,
			success: function(response) {
				if (response.vencido = 1) {
					var filas = response.data.length;
					for(i = 0; i < filas; i++) {
						var nuevafila = "<label>" + response.data[i]['Cliente'] + "</label><br>";
						$('#alerta_vencido').append(nuevafila);
					}
					
					$('#alerta_container').addClass('alerta_aparece');
					$('#alerta_container').addClass('alerta_se_mantiene');
				}
			},
			error:function(response) { 
				alert('Falló la respuesta del config.php; opc: alertas');
			},
		});
    },5000);
}

function cerrar_alerta(){
	$('#alerta_container').removeClass('alerta_aparece');
	$('#alerta_container').addClass('alerta_desaparece');
	$('#alerta_container').removeClass('alerta_se_mantiene');
}

function reseteo_combos(){
	$("#sel_cat_cliente").val("0");
	$("#sel_cat_monto").val("0");
	$("#sel_cat_plazo").val("0");
}

// Reestriccion de caracterres
jQuery(document).ready(function() {
   jQuery('#inp_nombre').keypress(function(tecla) {
      if((tecla.charCode >= 1 && tecla.charCode <= 31) || (tecla.charCode >= 33 && tecla.charCode <= 64) || (tecla.charCode >= 91 && tecla.charCode <= 96) || (tecla.charCode >= 123 && tecla.charCode <= 255) ) { 
			return false;
      }
   });
});

function alerta(frase){
	var camp_alert = frase;

	var delay = alertify.get('notifier','delay');
	alertify.set('notifier','position', 'top-center');
	alertify.set('notifier','delay', 2);
	alertify.warning(camp_alert);
	// alertify.set('notifier','delay', delay);
}