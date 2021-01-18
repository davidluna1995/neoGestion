<?php
/**
 * Este ejemplo envía el set de pruebas al SII para certificar boletas electrónicas
 * usando la API de SuperFactura.
 */
require_once('SuperFacturaAPI/api.php');

// Vamos a generar 10 boletas utilizando un mismo encabezado:
$encabezado = array(
	'IdDoc' => array(
		'TipoDTE' => 39,
		// 'FchEmis' => '2015-01-01', // Opcional
		/**
		 * Indicador Servicio:
		 * 1: Boletas de servicios periódicos
		 * 2: Boletas de servicios periódicos domiciliarios
		 * 3: Boletas de venta y servicios
		 * 4: Boleta de espectáculo emitida por cuenta de terceros
		 */
		'IndServicio' => 3,
	),
	'Emisor' => array(
		'RUTEmisor' => '99581150-2', // Indicar aquí el RUT de su contribuyente
		// Los demás datos serán agregados por SuperFactura
	),
	'Receptor' => array(
		'RUTRecep' => '1-9',
		'RznSocRecep' => 'Test',
		'Contacto' => 'cliente@dominio.cl',
	),
	// 'Totales' será agregado por SuperFactura
);

// --- Caso 1 ---

EnviarBoleta(array(
	'Encabezado' => $encabezado,
	'Detalles' => array(
		array(
			// 'NroLinDet' será agregado por SuperFactura
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
));

// --- Caso 2 ---

EnviarBoleta(array(
	'Encabezado' => $encabezado,
	'Detalles' => array(
		array(
			'NmbItem' => 'pizza española el italiano',
			'QtyItem' => 29,
			'PrcItem' => 2990,
		),
	),
));

// --- Caso 3 ---

EnviarBoleta(array(
	'Encabezado' => $encabezado,
	'Detalles' => array(
		array(
			'NmbItem' => 'sorpresa de cumpleaño',
			'QtyItem' => 90,
			'PrcItem' => 300,
		),
		array(
			'NmbItem' => 'gorros superhéroes',
			'QtyItem' => 13,
			'PrcItem' => 840,
		),
	),
));

// --- Caso 4 ---

EnviarBoleta(array(
	'Encabezado' => $encabezado,
	'Detalles' => array(
		array(
			'NmbItem' => 'item afecto 1',
			'QtyItem' => 12,
			'PrcItem' => 1500,
		),
		array(
			'IndExe' => 1,
			'NmbItem' => 'item exento 2',
			'QtyItem' => 2,
			'PrcItem' => 2590,
		),
		array(
			'IndExe' => 1,
			'NmbItem' => 'item exento 3',
			'QtyItem' => 1,
			'PrcItem' => 5000,
		),
	),
));

// --- Caso 5 ---

EnviarBoleta(array(
	'Encabezado' => $encabezado,
	'Detalles' => array(
		array(
			'NmbItem' => 'combo Italiano + bebida',
			'QtyItem' => 12,
			'PrcItem' => 1690,
		),
	),
));

// --- Caso 6 ---

EnviarBoleta(array(
	'Encabezado' => $encabezado,
	'Detalles' => array(
		array(
			'NmbItem' => 'item afecto 1',
			'QtyItem' => 5,
			'PrcItem' => 25,
		),
		array(
			'IndExe' => 1,
			'NmbItem' => 'item exento 2',
			'QtyItem' => 1,
			'PrcItem' => 20000,
		),
	),
));

// --- Caso 7 ---

EnviarBoleta(array(
	'Encabezado' => $encabezado,
	'Detalles' => array(
		array(
			'NmbItem' => 'goma de borrar school',
			'QtyItem' => 5,
			'PrcItem' => 340,
		),
	),
));

// --- Caso 8 ---

EnviarBoleta(array(
	'Encabezado' => $encabezado,
	'Detalles' => array(
		array(
			'NmbItem' => 'Té ceylan',
			'QtyItem' => 5,
			'PrcItem' => 3178,
		),
		array(
			'NmbItem' => 'Jugo super natural de 3/4 lts',
			'QtyItem' => 38,
			'PrcItem' => 150,
		),
	),
));

// --- Caso 9 ---

EnviarBoleta(array(
	'Encabezado' => $encabezado,
	'Detalles' => array(
		array(
			'NmbItem' => 'lápiz tinta azul',
			'QtyItem' => 10,
			'PrcItem' => 290,
		),
		array(
			'NmbItem' => 'lápiz tinta rojo',
			'QtyItem' => 5,
			'PrcItem' => 250,
		),
		array(
			'NmbItem' => 'lápiz tinta mágica',
			'QtyItem' => 3,
			'PrcItem' => 790,
		),
		array(
			'NmbItem' => 'lápiz corrector',
			'QtyItem' => 2,
			'PrcItem' => 1190,
		),
		array(
			'NmbItem' => 'corchetera',
			'QtyItem' => 1,
			'PrcItem' => 3500,
		),
	),
));

// --- Caso 10 ---

EnviarBoleta(array(
	'Encabezado' => $encabezado,
	'Detalles' => array(
		array(
			'NmbItem' => 'Clavo Galvanizado 3/4"',
			'QtyItem' => 3.8,
			'PrcItem' => 710,
			'UnmdItem' => 'KG',
		),
	),
));

// Esta es una función auxliar que realiza el envío de las boletas
// usando la API de SuperFactura.
function EnviarBoleta($datos) {
	$api = new SuperFacturaAPI('usuario@cliente.cl', 'mypassword'); // Usar aquí los datos de su cuenta
	$resultado = $api->SendDTE($datos, 'cer'); // 'pro' = ambiente producción y 'cer' = ambiente certificación

	// Procesar salida de la API para obtener el folio asignado.

	if($resultado['ok']) {
		echo "Folio #{$resultado['folio']} is ok.<br>";

	} else {
		die('Error');
	}
}
