<?php
error_reporting(0);
date_default_timezone_set('UTC');

global $facturas;
global $producto;

$facturas = [];
$producto = [];

function AgregarProducto($numero,$imp, $descripcion, $unit){
	$url = 'http://localhost/Tarea-6-RestFull/server.php/producto';

	$data = array('qty'=>$imp,'descripcion'=>$descripcion,'valor' => $unit, 'factura' => $numero);
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'PUT',
			'content' => http_build_query($data),
		)
	);

	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	calcularImp($numero);
}

function cargarfacturas(){
	$url = 'http://localhost/Tarea-6-RestFull/server.php/factura';
	$result = file_get_contents($url);
	$data = json_decode($result, true);

	return $data;
}

function cargarproducto($id){
	$url = 'http://localhost/Tarea-6-RestFull/server.php/producto/'.$id;
	$result = file_get_contents($url);
	$data = json_decode($result, true);

	return $data;
}
function calcularImp($numero){
	$url = 'http://localhost/Tarea-6-RestFull/server.php/factura/'.$numero;

	$data = array('number'=>$numero);
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data),
		)
	);

	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
}


function eliminar_factura($id){
	$url = 'http://localhost/Tarea-6-RestFull/server.php/factura/'.$id;

	$data = array('id'=>$id);
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'DELETE',
			'content' => http_build_query($data),
		)
	);

	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
}

function guardar_factura($id,$cli,$fecha){
	$url = 'http://localhost/Tarea-6-RestFull/server.php/factura';

	$data = array('number'=>$id,'fecha'=>$fecha,'cliente' => $cli);
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data),
		)
	);

	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
}

function agregarfactura($numero, $fecha, $cliente){
	$url = 'http://localhost/Tarea-6-RestFull/server.php/factura';

	$data = array('number'=>$numero,'fecha'=>$fecha,'cliente' => $cliente);
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'PUT',
			'content' => http_build_query($data),
		)
	);

	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
}
function eliminar_producto($id_P,$id_F){
	$url = 'http://localhost/Tarea-6-RestFull/server.php/producto/'.$id_P;

	$data = array('id'=>$id_P);
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'DELETE',
			'content' => http_build_query($data),
		)
	);

	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	calcularImp($id_F);
}
if($_SERVER["REQUEST_METHOD"] == "POST") {

	if($_POST['agregar_factura']){
		$agregar_numero = $_POST['agregar_numero'];
		$agregar_fecha = $_POST['agregar_fecha'];
		$agregar_cliente = $_POST['agregar_cliente'];

		agregarfactura($agregar_numero, $agregar_fecha, $agregar_cliente);
	}
	if($_POST['detalles_factura']){
		$num_factura = $_POST['factura_id'];

		$url = 'http://localhost/Tarea-6-RestFull/server.php/factura/'.$num_factura;
		$result = file_get_contents($url);
		$data = json_decode($result, true);

		$id = $data[0]['id'];
		$cliente = $data[0]['cliente'];
		$fecha = $data[0]['fecha'];
		$imp = $data[0]['imp'];
		$total_f = $data[0]['total_f'];

	}
	if($_POST['guardar_factura']){
		$numeroGuardar = $_POST['number'];

		$clienteGuardar = $_POST['cliente_factura'];
		$fechaGuardar = $_POST['fecha_factura'];
		$editar_fecha = $_POST['editar_fecha'];

		guardar_factura($numeroGuardar, $clienteGuardar, $fechaGuardar != null ? $fechaGuardar : $editar_fecha);
	}
	if($_POST['eliminar_producto']){
		$numero = $_POST['id_prod'];
		$numero2 = $_POST['number'];

		eliminar_producto($numero,$numero2);
	}


	if($_POST['agregar_producto']){
		$numero = $_POST['numero_array'];
		$producto_numero = $_POST['producto_cantidad'];
		$producto_descripcion = $_POST['producto_descripcion'];
		$producto_unit = $_POST['producto_unit'];

		AgregarProducto($numero, $producto_numero, $producto_descripcion, $producto_unit);
	}
	if($_POST['eliminar_factura']){
		$numero = $_POST['number'];

		eliminar_factura($numero);
	}

}

$facturas = cargarfacturas();

if($num_factura){
	$producto = cargarproducto($num_factura);
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Tarea 6</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
	<div class="row">
		<div class="col-lg-3">
			<h1>Facturas</h1>

			<table class="table">
				<thead class="thead-dark">
					<tr class="d-flex">
						<th class="col-0"> </th>
						<th class="col-3">Id</th>
						<th class="col-3">cliente</th>
						<th class="col-3">Acción</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($facturas as $key => $row)
					{
						echo('<tr class="d-flex">');
						echo('<form method="post" action="">');
						echo('<td class="col-sm-0"><input type="hidden" value="'.$row['id'].'" name="factura_id"/</td>');
						echo('<td class="col-sm-3">'.$row['id'].'</td>');
						echo('<td class="col-sm-3">'.$row['cliente'].'</td>');
						echo('<td class="col-sm-2"><input name="detalles_factura" class="btn btn-info" type="submit" value="Detalles"></td>');
						echo('</form>');
						echo('</tr>');
					}
					?>
				</tbody>
			</table>
		</div>
		<div class="col-md-9 col-lg-9">
			<h1> Agregar Facturas</h1>
			<div>
				<form action="" method="post">
					<label>Id: <input name="agregar_numero" type="number"/></label>
					<label>fecha: <input name="agregar_fecha" type="date"/></label>
					<label>cliente: <input name="agregar_cliente" type="text"/></label>
					<input type="submit" value="Agregar factura" class="btn btn-info" name="agregar_factura"/>
				</form>
			</div>
			<form action="" method="post">

				<table class="table table-border">
					<tbody>
						<?php
						echo "
						<tr>
						<td><label>Id: <input type=\"number\" name=\"number\" value=\"$id\" readonly/> </label></td>

						<td><label>Fecha: <input type=\"text\" name=\"editar_fecha\" value=\"$fecha\" readonly> Edit fecha: <input type=\"date\" name=\"fecha_factura\"/> </label></td>
						</tr>

						<tr>
						<td><label>Cliente: <input type=\"text\" value=\"$cliente\" name=\"cliente_factura\"/> </label></td>
						</tr>";
						?>
					</tbody>
				</table>
				<table class="table table-border">
					<thead class="thead-dark">
						<tr>
							<th>Cantidad</th>
							<th>Descripcion</th>
							<th>Valor Unidad</th>
							<th>Subtotal</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($producto as $Prod_key => $valor){
							echo ('
							<form action="" method="post">
							<tr>
							<input type="hidden" name="number" value="'.$valor['factura'].'"/>
							<input type="hidden" value="'.$valor['id'].'" name="id_prod"/>

							<td><label> '.$valor['cantidad'].'</label></td>

							<td><label> '.$valor['descripcion'].'</label></td>

							<td><label> '.$valor['valor'].'</label></td>

							<td><label> '.$valor['subtotal'].'</label></td>

							<td><input type="submit" value="borrar producto" class="btn btn-success" name="eliminar_producto"/></td>
							</tr>
							</form>');
						}
						if($id){
							echo "
							<input type=\"hidden\" value=\"$num_factura\" name=\"numero_array\"/>
							<tr>
							<td><input type=\"number\" name=\"producto_cantidad\"/></td>

							<td><input type=\"text\" name=\"producto_descripcion\"/></td>

							<td><input type=\"number\" name=\"producto_unit\"/></td>

							<td></td>

							<td><input type=\"submit\" value=\"Agregar producto\" class=\"btn btn-success\" name=\"agregar_producto\"/></td>
							</tr>";
						}
						echo "
						<tr>
						<td><input type=\"submit\" value=\"Eliminar\" class=\"btn btn-dark\" name=\"eliminar_factura\"/></td>

						<td><label>Impuesto $imp </label></td>

						<td><label>Total $total_f</label></td>

						<td></td>

						<td><input type=\"submit\" value=\"Guardar\" class=\"btn btn-dark\" name=\"guardar_factura\"/></td>
						</tr>
						";
						?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
	<br/>
	</body>
</html>
