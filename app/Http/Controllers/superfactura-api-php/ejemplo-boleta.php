<?php
require_once('SuperFacturaAPI/api.php');

$datos = array(
	'Encabezado' => array(
		'IdDoc' => array(
			'TipoDTE' => 39, // Boleta electr�nica de ventas
			// 'FchEmis' => '2015-01-01', // Opcional
			'IndServicio' => 3, // Boletas de venta y servicios
		),
		'Emisor' => array(
			'RUTEmisor' => '99581150-2', // Indicar aqu� el RUT de su contribuyente
			// Los dem�s datos ser�n agregados por SuperFactura
		),
		
		'Receptor' => array(
			'RUTRecep' => '1-9',
			'RznSocRecep' => 'Test',
			'Contacto' => 'cliente@dominio.cl',
		),
		// 'Totales' ser� agregado por SuperFactura
	),
	'Detalles' => array(
		array(
			// 'NroLinDet' ser� agregado por SuperFactura
			'NmbItem' => 'koyak el chupete',
			// 'DscItem' => 'koyak el chupete',
			'QtyItem' => 12,
			// 'UnmdItem' => 'KG',
			'PrcItem' => 170,
		),
		array(
			'NmbItem' => 'cuaderno pre U',
			'QtyItem' => 20,
			'PrcItem' => 1050,
		),
	),	
);

// Enviar boleta con la API

$api = new SuperFacturaAPI('usuario@cliente.cl', 'mypassword'); // Usar aqu� los datos de su cuenta
$resultado = $api->SendDTE($datos, 'cer'); // 'pro' = ambiente producci�n y 'cer' = ambiente certificaci�n

// Procesar salida de la API para obtener el folio asignado.

if($resultado['ok']) {
	echo "La Boleta #{$resultado['folio']} fue enviada correctamente.<br>";

} else {
	die('Error');
}
