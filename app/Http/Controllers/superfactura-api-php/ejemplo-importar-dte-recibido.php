<?php
/**
 * Este ejemplo muestra como importar un documento recibido a SuperFactura.
 */

require_once('SuperFacturaAPI/api.php');

// 1) Generar arreglo con datos del DTE recibido

$datos = array(
	'Encabezado' => array(
		'IdDoc' => array(
			'Folio' => 123,
			'TipoDTE' => 33,
			'FchEmis' => '2015-01-01',
		),
		'Emisor' => array(
			'RUTEmisor' => '1-9',
			'RznSoc' => 'Test',
			'GiroEmis' => 'Giro',
			'DirOrigen' => 'Dirección',
			'CmnaOrigen' => 'Comuna',
			'CiudadOrigen' => 'Ciudad',
		),
		'Receptor' => array(
			// Acá basta con proporcionar el RUT de nuestro contribuyente
			'RUTRecep' => '99581150-2',
		),
		// 'Totales' será agregado por SuperFactura
	),
	'Detalles' => array(
		array(
			// 'NroLinDet' será agregado por SuperFactura
			'NmbItem' => 'Item 1',
			'DscItem' => 'Descripción del item 1',
			'QtyItem' => 3,
			'UnmdItem' => 'KG',
			'PrcItem' => 100,
		),
		array(
			'NmbItem' => 'Item 2',
			'DscItem' => 'Descripción del item 2',
			'QtyItem' => 5,
			'UnmdItem' => 'KG',
			'PrcItem' => 65,
		)
	),
);

// 2) Usar API para importra el DTE a SuperFactura

$api = new SuperFacturaAPI('usuario@cliente.cl', 'mypassword');
$resultado = $api->SendDTE($datos, 'cer', array( // 'pro' = ambiente producción y 'cer' = ambiente certificación
	'import' => 'recibido', // Indica que no queremos generar un DTE, sino importarlo como un documento recibido.
));

// 3) Procesar salida de la API

if($resultado['ok']) {
	die("Ok");

} else {
	die('Error');
}
