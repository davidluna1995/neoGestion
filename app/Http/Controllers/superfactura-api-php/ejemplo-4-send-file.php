<?php
/**
 * Similar al ejemplo-2, pero se envía un archivo JSON o XML.
 */

header('Content-type: text/html; charset=iso-8859-1'); // Opcional (sólo para asegurar charset correcto)

require_once('SuperFacturaAPI/api.php');

$filename = '/tmp/test.json';
if(!file_exists($filename)) die("ERROR: No existe el archivo '$filename'.\n");

// 1) Leer datos desde archivo

$data = array(
    'content' => implode('', file($filename))
);

// 2) Usar API para generar y enviar el DTE al SII

$api = new SuperFacturaAPI('usuario@cliente.cl', 'mypassword');

$resultado = $api->SendDTE(
	$datos,	// Datos del DTE
	'cer',	// Ambiente: 'pro' = producción y 'cer' = certificación
	array(	// El tercer argumento puede contener un arreglo con opciones
		'savePDF' => '/tmp/dte-123',	// Obtiene y guarda el PDF
		'saveXML' => '/tmp/dte-123',	// Obtiene y guarda el XML
	)
);

// 3) Procesar salida de la API

if($resultado['ok']) {
	echo "Folio: {$resultado['folio']}<br>";
	die('ok');

} else {
	die('Error');
}
