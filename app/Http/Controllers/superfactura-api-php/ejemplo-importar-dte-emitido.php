<?php
/**
 * Este ejemplo muestra como importar un documento emitido a SuperFactura.
 */

require_once('SuperFacturaAPI/api.php');

// 1) Generar arreglo con datos del documento emitido

$datos = array(
	'Encabezado' => array(
		'IdDoc' => array(
			'Folio' => 1,
			'TipoDTE' => 30,
			'FchEmis' => '2015-01-01',
		),
		'Emisor' => array(
			// Ac� basta con proporcionar el RUT de nuestro contribuyente
			'RUTEmisor' => '99581150-2',
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

// 2) Usar API para importra el DTE a SuperFactura

$api = new SuperFacturaAPI('usuario@cliente.cl', 'mypassword');
$resultado = $api->SendDTE($datos, 'cer', array( // 'pro' = ambiente producci�n y 'cer' = ambiente certificaci�n
	'import' => 'emitido', // Indica que no queremos generar un DTE, sino importarlo como un documento emitido.
));

// 3) Procesar salida de la API

if($resultado['ok']) {
	die("Ok");

} else {
	die('Error');
}
