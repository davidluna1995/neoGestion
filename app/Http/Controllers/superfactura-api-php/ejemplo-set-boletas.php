<?php
/**
 * Este ejemplo env�a el set de pruebas al SII para certificar boletas electr�nicas
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
		 * 1: Boletas de servicios peri�dicos
		 * 2: Boletas de servicios peri�dicos domiciliarios
		 * 3: Boletas de venta y servicios
		 * 4: Boleta de espect�culo emitida por cuenta de terceros
		 */
		'IndServicio' => 3,
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
);

// --- Caso 1 ---

EnviarBoleta(array(
	'Encabezado' => $encabezado,
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
));

// --- Caso 2 ---

EnviarBoleta(array(
	'Encabezado' => $encabezado,
	'Detalles' => array(
		array(
			'NmbItem' => 'pizza espa�ola el italiano',
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
			'NmbItem' => 'sorpresa de cumplea�o',
			'QtyItem' => 90,
			'PrcItem' => 300,
		),
		array(
			'NmbItem' => 'gorros superh�roes',
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
			'NmbItem' => 'T� ceylan',
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
			'NmbItem' => 'l�piz tinta azul',
			'QtyItem' => 10,
			'PrcItem' => 290,
		),
		array(
			'NmbItem' => 'l�piz tinta rojo',
			'QtyItem' => 5,
			'PrcItem' => 250,
		),
		array(
			'NmbItem' => 'l�piz tinta m�gica',
			'QtyItem' => 3,
			'PrcItem' => 790,
		),
		array(
			'NmbItem' => 'l�piz corrector',
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

// Esta es una funci�n auxliar que realiza el env�o de las boletas
// usando la API de SuperFactura.
function EnviarBoleta($datos) {
	$api = new SuperFacturaAPI('usuario@cliente.cl', 'mypassword'); // Usar aqu� los datos de su cuenta
	$resultado = $api->SendDTE($datos, 'cer'); // 'pro' = ambiente producci�n y 'cer' = ambiente certificaci�n

	// Procesar salida de la API para obtener el folio asignado.

	if($resultado['ok']) {
		echo "Folio #{$resultado['folio']} is ok.<br>";

	} else {
		die('Error');
	}
}
