<?php
date_default_timezone_set('America/Mazatlan');

// DATOS DE CONECCION AL SERVIDOR 
$server = '127.0.0.1';
$bd = 'sandbox';
$user = 'root';
$password = '';

$dsn = "mysql:host=$server;dbname=$bd";
$dbh = new PDO($dsn, $user, $password) or die ('Error al conectarse al servidor');

switch ($_POST['opc']) {
	case 'registrar_prestamo': registrar_prestamo($server,$bd,$user,$password);
		break;

	case 'buscar_prestamo_cliente': buscar_prestamo_cliente($server,$bd,$user,$password);
		break;

	case 'cargar_datos_pagos': cargar_datos_pagos($server,$bd,$user,$password);
		break;

	case 'alertas': alertas($server,$bd,$user,$password);
		break;
}

function registrar_prestamo($server,$bd,$user,$password){
	$Cliente = $_REQUEST['cliente'];
	$IdCliente = intval($_REQUEST['idcliente']);
	$Monto = $_REQUEST['monto'];
	$Plazo = $_REQUEST['plazo'];
	$Fecha = date("Y-m-d H:i:s");

	$Semanas = $Plazo * 2;
	$FechaLimite = date("Y-m-d",strtotime($Fecha."+ $Semanas week"));

	$dsn = "mysql:host=$server;dbname=$bd";
	$dsn_Options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
	$dbh = new PDO($dsn, $user, $password, $dsn_Options) or die ('Error al conectarse al servidor');
	$query = $dbh->prepare("INSERT INTO sandbox.historial_prestamos (IdCliente,Cliente,Monto,Plazo,FechaPrestamo,FechaLimite) VALUES (?,?,?,?,?,?);");
	
	$query->bindParam(1, $IdCliente);
	$query->bindParam(2, $Cliente);
	$query->bindParam(3, $Monto);
	$query->bindParam(4, $Plazo);
	$query->bindParam(5, $Fecha);
	$query->bindParam(6, $FechaLimite);

	if ($query->execute()) {
		$Estado = 1;
		$Mensaje = 'Datos insertados';
	} else {
		$Estado = 0;
		$Mensaje = 'Datos no insertados';
	}

	$retonar = array('estado' => $Estado ,'mensaje' => $Mensaje);
	echo json_encode($retonar);
}

function buscar_prestamo_cliente($server,$bd,$user,$password){
	$Cliente = $_REQUEST['cliente'];

	$dsn = "mysql:host=$server;dbname=$bd";
	$dsn_Options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
	$dbh = new PDO($dsn, $user, $password, $dsn_Options) or die ('Error al conectarse al servidor');
	$query = $dbh->prepare("SELECT IdPrestamo,IdCliente,Cliente, Monto, CAST(FechaPrestamo AS DATE) AS Fecha, Plazo FROM sandbox.historial_prestamos WHERE Cliente LIKE '".$Cliente."%' ORDER BY Cliente;");

	$query->execute();
	$data = $query->fetchAll();

	if (empty($data)) {
		$Estado = 0;
		$Mensaje = 'No hay prestamos a ese nombre';
	} else {
		$Estado = 1;
		$Mensaje = 'Clientes encontrados';
	}
	
	$retonar = array('estado' => $Estado ,'mensaje' => $Mensaje, 'prestamos' => $data);
	echo json_encode($retonar);
}

function cargar_datos_pagos($server,$bd,$user,$password){
	$IdPrestamo = $_REQUEST['idprestamo'];

	$dsn = "mysql:host=$server;dbname=$bd";
	$dsn_Options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
	$dbh = new PDO($dsn, $user, $password, $dsn_Options) or die ('Error al conectarse al servidor');
	$query = $dbh->prepare("SELECT Cliente, Monto, Plazo, CAST(FechaPrestamo AS DATE) AS FechaPrestamo, FechaLimite FROM sandbox.historial_prestamos WHERE IdPrestamo = ".$IdPrestamo.";");

	if ($query->execute()) {
		$Estado = 1;
		$Mensaje = 'Datos de prestamo encontrados';
	} else {
		$Estado = 0;
		$Mensaje = 'No se encontraron datos de este prestamo';
	}
	
	$data = $query->fetchAll();

	$MontoConInteres = bcdiv($data[0][1] * 1.11, '1', 2);

	$dbh = null;
	$dbh = new PDO($dsn, $user, $password, $dsn_Options) or die ('Error al conectarse al servidor');
	$query = $dbh->prepare("SELECT  ROW_NUMBER() OVER (ORDER BY IdAbono) AS rn, CAST(FechaAbono AS DATE) AS FechaAbono, (((MontoAbono / 1.11)-MontoAbono) * -1) AS interes, MontoAbono, IdAbono FROM sandbox.historial_pagos WHERE IdPrestamo = ".$IdPrestamo." ORDER BY FechaAbono;");

	if ($query->execute()) {
		$EstadoPrestamo = 1;
		$MensajePrestamo = 'Datos de abonos';
	} else {
		$EstadoPrestamo = 0;
		$MensajePrestamo = 'No se encontraron datos de los abonos de este prestamo';
	}
	
	$data_Prestamo = $query->fetchAll();

	empty($data_Prestamo) ? $Estado_datos_prestamo = 0 : $Estado_datos_prestamo = 1;

	$retonar = array(
		'estado' => $Estado, 
		'estado_prestamo' => $EstadoPrestamo, 
		'mensaje' => $Mensaje,
		'mensaje_prestamo' => $MensajePrestamo, 
		'dato_prestamo' => $data, 
		'dato_prestamo_tabla' => $data_Prestamo,
		'monto_pagar' => $MontoConInteres,
		'estado_datos_prestamo' => $Estado_datos_prestamo
	);
	
	echo json_encode($retonar);	
}

function alertas($server,$bd,$user,$password){
	$dsn = "mysql:host=$server;dbname=$bd";
	$dsn_Options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
	$dbh = new PDO($dsn, $user, $password, $dsn_Options) or die ('Error al conectarse al servidor');
	$query = $dbh->prepare("SELECT A.Cliente FROM sandbox.historial_prestamos AS A INNER JOIN (SELECT IdPrestamo, SUM(MontoAbono) AS MontoAbonado FROM sandbox.historial_pagos GROUP BY IdPrestamo) AS B ON (A.IdPrestamo = B.IdPrestamo) WHERE DATEDIFF(A.FechaLimite,NOW()) < 0 AND ((A.Monto * 1.11) - B.MontoAbonado) > 0;");

	if ($query->execute()) {

		$data = $query->fetchAll();
		empty($data) ? $Vencidos = 0 : $Vencidos = 1;
		$retonar = array('vencidos' => $Vencidos, 'data' => $data);
		echo json_encode($retonar);

	} else {
		echo 'Error em la ejecución del query de la funcion: alertas()';
	}
}
?>