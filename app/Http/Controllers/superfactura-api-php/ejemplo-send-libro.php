<?php
require_once('SuperFacturaAPI/api.php');

// 1) Definir arreglo con XML del libro

$datos = array(
	'xml' => $xml // XML del libro
);

// 2) Enviar al SII usando la API de SuperFactura

$api = new SuperFacturaAPI('usuario@cliente.cl', 'mypassword');
$resultado = $api->SendDTE($datos, 'cer'); // 'pro' = ambiente producci�n y 'cer' = ambiente certificaci�n

// 3) Procesar salida de la API

if($resultado['ok']) {
	die("Ok");

} else {
	die('Error');
}
