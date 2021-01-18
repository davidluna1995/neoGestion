<?php
require_once('SuperFacturaAPI/api.php');

$api = new SuperFacturaAPI('usuario@cliente.cl', 'mypassword');

$getXML = false; // Usar 'true' para obtener XML completo
$res = $api->GetDocsRecibidos('99581150-2', '2017-11', $getXML);

echo '<pre>';
foreach($res as $dte) {
	print_r($dte);
}
