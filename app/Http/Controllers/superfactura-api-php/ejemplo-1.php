<?php
require_once('SuperFacturaAPI/api.php');

// 1) Generar arreglo con datos del DTE

$datos = array(
	'Encabezado' => array(
		'IdDoc' => array(
			'TipoDTE' => 33,
			// 'FchEmis' => '2015-01-01', // Opcional
		),
		'Emisor' => array(
			'RUTEmisor' => '99581150-2',
			// Los dem�s datos ser�n agregados por SuperFactura
		),
		'Receptor' => array(
			'RUTRecep' => '1-9',
			'RznSocRecep' => 'Test',
			'GiroRecep' => 'Giro',
			'DirRecep' => 'Direcci�n',
			'CmnaRecep' => 'Comuna',
			'CiudadRecep' => 'Ciudad',
		),
		// 'Totales' ser� agregado por SuperFactura
	),
	'Detalles' => array(
		array(
			// 'NroLinDet' ser� agregado por SuperFactura
			'NmbItem' => 'Item 1',
			'DscItem' => 'Descripci�n del item 1',
			'QtyItem' => 3,
			'UnmdItem' => 'KG',
			'PrcItem' => 100,
		),
		array(
			'NmbItem' => 'Item 2',
			'DscItem' => 'Descripci�n del item 2',
			'QtyItem' => 5,
			'UnmdItem' => 'KG',
			'PrcItem' => 65,
		)
	),
);

// 2) Usar API para generar y enviar el DTE al SII

$api = new SuperFacturaAPI('usuario@cliente.cl', 'mypassword');
$resultado = $api->SendDTE($datos, 'cer'); // 'pro' = ambiente producci�n y 'cer' = ambiente certificaci�n

// 3) Procesar salida de la API

if($resultado['ok']) {
	die("Ok. Folio: {$resultado['folio']}");

} else {
	die('Error');
}
