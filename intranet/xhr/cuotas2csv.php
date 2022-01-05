<?
// Creamos la conexión con la BD
require("lib/CreaConexion.php");
$fecha_cuota=$_REQUEST['fecha'];
$clave=$_REQUEST['filtro'];
if($clave){
	switch($clave){
		case "D":
			$condicion=" AND domicilia_bco";
			break;
		case "S":
			$condicion=" AND NOT domicilia_bco";
			break;
		default:
			$condicion="";
	}
}
if($fecha_cuota){
	$fecha_cuota="'$fecha_cuota'";
}else{
	$fecha_cuota="(SELECT MAX(fecha) FROM cuotas)";
}
// Consultamos la BD
$conexion->connect('openrarp') or die('Error al conectar con esta BD');

$query = "SELECT id_parcela,titular,fecha,cuota,domiciliado
		FROM vista_cuotas WHERE fecha=$fecha_cuota $condicion ORDER BY id_parcela";
$id_result=@$conexion->query($query) or die('Error al hacer la query');
$num_filas=@$conexion->num_rows($id_result);
//echo "$query";
$i=0;
$cabecera='id_parcela,titular,fecha,cuota,domiciliado'."\r\n";
//echo $cabecera;
while ($i < $num_filas){
	$fila=@$conexion->fetch_array($id_result,$i);
	$id_parcela=$fila['id_parcela'];
	$titular=$fila['titular'];
	$fecha=$fila['fecha'];
	$cuota=$fila['cuota'];
	$domiciliado=$fila['domiciliado'];
	$cuotas .= "$id_parcela;$titular;$fecha;$cuota;$domiciliado"."\r\n";
$i++;
}
$contenido=$cabecera.$cuotas;
//echo $resultado;

$anyo=date('y');
$mes=date('m');
$dia=date('d');
$tiempo_touch=strtotime($dia.'-'.$mes.'-'.$anyo);
touch('../temp/cuotas.csv',$tiempo_touch);
if (is_writable('../temp/cuotas.csv')) {
	if (!$gestor = fopen('../temp/cuotas.csv', 'w')) {
		echo "No se puede abrir el archivo cuotas.csv";
		exit;
	}else{
		fwrite($gestor, $contenido) or die('Error al escribir el fichero'); 
		echo "OK";
	}
}
fclose($gestor);
?>
