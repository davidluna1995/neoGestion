<?php
/**
 * En este ejemplo, se agregan descuentos generales con <DscRcgGlobal>.
 * Adem�s, se agregan otros impuestos especiales con <CodImpAdic> y <ImptoReten>.
 */

header('Content-type: text/html; charset=iso-8859-1'); // Opcional (s�lo para asegurar charset correcto)

require_once('SuperFacturaAPI/api.php');

// 1) Generar arreglo con datos del DTE

$datos = array(
	'Encabezado' => array(
		'IdDoc' => array(
			'TipoDTE' => 52,
			// <Folio> es agregado por SuperFactura
			'FchEmis' => '2015-04-09',
			'IndTraslado' => 5,
			'FmaPago' => 1
		),
		'Emisor' => array(
			'RUTEmisor' => '99581150-2',
			// Los dem�s datos son agregados por SuperFactura
			'CdgVendedor' => 'XXX'
		),
		'Receptor' => array(
			'RUTRecep' => '1-9',
			'RznSocRecep' => 'Test',
			'GiroRecep' => 'Giro',
			'CorreoRecep' => 'dte@cliente.cl',
			'DirRecep' => 'Direcci�n',
			'CmnaRecep' => 'Comuna',
			'CiudadRecep' => 'Ciudad',
		),
		'Totales' => array(
			'MntNeto' => 238883,
			'MntExe' => 0,
			'TasaIVA' => 19.00,
			'IVA' => 45388,
			/* El tag <ImptoReten> es agregado autom�ticamente por SuperFactura
			'ImptoReten' => array(
				array(
					'TipoImp' => 26,
					'TasaImp' => 31.50,
					'MontoImp' => 6751,
				),
				array(
					'TipoImp' => 27,
					'TasaImp' => 31.50,
					'MontoImp' => 6751,
				),
			),
			*/
			'MntTotal' => 327600,
		),
	),
	'Detalle' => array(
		array(
			// 'NroLinDet' => 1, // Opcional. Agregado por SuperFactura
			'CdgItem' => array(
				'TpoCodigo' => 'INT',
				'VlrCodigo' => '75032715',
			),
			'NmbItem' => 'CORONA EXTRA 355 CC 4.6',
			'DscItem' => 'CORONA EXTRA 355 CC 4.6',
			'UnmdRef' => 'UNI',
			'QtyItem' => 72,
			'UnmdItem' => 'UNI',
			'PrcItem' => 440,
			'CodImpAdic' => 26, // Tasa del 15%. Cervezas y bebidas alcoh�licas
			// 'MontoItem' => ..., // Opcional. Agregado por SuperFactura
		),
		array(
			'NmbItem' => 'Coca-Cola',
			'DscItem' => 'Coca-Cola',
			'UnmdRef' => 'UNI',
			'QtyItem' => 36,
			'UnmdItem' => 'UNI',
			'PrcItem' => 911,
			'CodImpAdic' => 271, // Tasa del 18%. Bebidas analcoh�licas y Minerales con elevado contenido de az�cares, seg�n DL 825/74, Art. 42, letra a) Inciso Segundo
		),
		// Se omitieron otros detalles
	),
	'DscRcgGlobal' => array(
		array(
			'TpoMov' => 'D', // 'D' = descuento, 'R' = recargo
			'GlosaDR' => 'Para facilitar cuadratura autom�tica',
			'TpoValor' => '$', // '$' = en pesos, '%' = porcentual
			'ValorDR' => 1, // valor del descuento
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
