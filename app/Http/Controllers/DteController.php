<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Configuraciones;
use App\DetalleVenta;
use App\Documentoxml;
use App\Documentoxmlenvio;
use App\emisor_receptor_venta;
use App\Producto;
use App\RegistroCajaVendedor;
use App\Ventas;
use DOMDocument;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;
use SuperFacturaAPI;

class DteController extends Controller
{
    public $config = [
        'firma' => [
            'file' => '/Users/alejandroesteban/Downloads/firma_digital_certificado.pfx',
            //'data' => '', // contenido del archivo certificado.p12
            'pass' => '1028',
        ],
    ];


    public function ver_antes_dte_33(Request $r){
        $new = [];
        $emisor = Configuraciones::all();

        $emisor['emisor'] = $emisor[0];

        $new['Llave'] = 'test key';
        $new['Fecha'] = $r->fecha;

        if($r->sii_forma_pago == 'CONTADO'){
            $new['FormaPago'] = 0;

        }
        if($r->sii_forma_pago == 'CREDITO'){
            $new['FormaPago'] = 1;

        }
        $new['FormaPago_str'] = $r->sii_forma_pago;

        $new['emisor'] = $emisor['emisor'];
        $new['emisor']['empresa'] = strtoupper($emisor[0]['empresa']);
        $new['emisor']['giro'] = strtoupper($emisor[0]['giro']);
        $new['emisor']['rut'] = strtoupper($emisor[0]['rut']);
        $new['emisor']['dirreccion'] = strtoupper($emisor[0]['dirreccion']);

        $new['Cliente']['Ciudad'] = strtoupper($r->reseptor['ciudad']);
        $new['Cliente']['RazonSocial'] = strtoupper($r->reseptor['cliente']);
        $new['Cliente']['Comuna'] = strtoupper($r->reseptor['comuna']);
        $new['Cliente']['Contacto'] = strtoupper($r->reseptor['contacto']);
        $new['Cliente']['Direccion'] = strtoupper($r->reseptor['direccion']);
        $new['Cliente']['Email'] = strtoupper($r->reseptor['email']);
        $new['Cliente']['Giro'] = strtoupper($r->reseptor['giro']);
        $new['Cliente']['Rut'] = strtoupper($r->reseptor['rut']);

        $i=0;
       foreach ($r->carro as $c) {
            $new['Productos'][$i]['id'] = $c['id'];
            $new['Productos'][$i]['NombreProducto'] = strtoupper($c['nombre']);
            $new['Productos'][$i]['DescripcionAdicional'] = strtoupper('...');
            $new['Productos'][$i]['Afecto'] = $c['afecto'];
            $new['Productos'][$i]['Cantidad'] = $c['cantidad_ls'];
            $new['Productos'][$i]['PrecioNeto'] = $c['precio'];
            $new['Productos'][$i]['DescuentoNeto'] = $c['monto_descuento']/*$c['descuento']*/;
            $new['Productos'][$i]['PorcentajeDescuentoNeto'] = $c['descuento'];
            $new['Productos'][$i]['TipoImpAdicional'] =$c['tipo_impuesto_adicional'];
            $new['Productos'][$i]['MontoImpAdicional'] = $c['monto_impuesto_adicional'];
            $new['Productos'][$i]['UnidadMedida'] = $c['unidad'];
            $new['Productos'][$i]['SubTotal'] =$c['item_descontado'];

            $i++;
       }



        // $r->reseptor->ciudad =strtoupper($r['reseptor']['ciudad']);
        // $r = array_map('strtoupper', $r);
        return ['estado'=>'success', 'factura'=>$new, 'factura_origin' => $r->all()];


        return ['estado'=>'success', 'factura'=>$r->all()];


    }


    public function emitir_dte_33(Request $r){


        $venta_maxima = $this->registro_venta($r);

        return $venta_maxima;
    }

    public function registro_venta(Request $datos)
    {

        $vuelto=0;
        $verify_caja_activa = RegistroCajaVendedor::where([
                                    'activo' => 'S',
                                    'user_id' => Auth::user()->id
                                ])->first();

            if($verify_caja_activa){
                    $reg = $verify_caja_activa;
                    $r_c_v_id = $reg->id;
            }else{
                $r_c_v_id = 0;
            }

            if( $r_c_v_id == 0){
                return ['estado'=>'failed', 'mensaje'=>'La caja no esta activa'];
            }


            DB::beginTransaction();
            $venta = new Ventas();
            $venta->user_id = Auth::user()->id;
            $venta->registro_caja_vendedor_id = $r_c_v_id;
            $venta->tipo_venta_id = $datos->tipo_venta_id;

            if ($datos->venta_total == '0') {
                return ['estado'=>'failed', 'mensaje'=>'ingrese minimo un producto al carro.'];
            } else {
                $venta->venta_total = $datos->totales['Total'];
                $venta->pago_efectivo = !empty($datos->efectivo) ?  $datos->efectivo : '0';
                $venta->pago_debito = !empty($datos->debito) ? $datos->debito : '0';

            }

            //validar si fuera forma de pago CONTADO
            if($datos->sii_forma_pago == 'CONTADO'){

                if ($datos->forma_pago_id == '1,undefined') {
                    $venta->forma_pago_id = '1';
                    if($datos->efectivo == 0 || trim($datos->efectivo)==''){
                        return ['estado'=>'failed', 'mensaje'=>'ingrese el monto en efectivo.'];
                    }
                    // $vuelto = (int)$datos->pago_efectivo - (int)$datos->venta_total;
                    $vuelto = $datos->vuelto;
                    if($vuelto < 0){
                        return ['estado' => 'failed', 'mensaje' => 'La deuda del cliente es '.abs($vuelto).', '];
                    }
                    $venta->vuelto = $vuelto;
                } elseif ($datos->forma_pago_id == '2,undefined') {
                    $venta->forma_pago_id = '2';
                    if($datos->debito == 0 || trim($datos->debito)==''){
                        return ['estado'=>'failed', 'mensaje'=>'ingrese el monto en efectivo.'];
                    }
                    // $vuelto = (int)$datos->debito - (int)$datos->total;
                    $vuelto = $datos->vuelto;
                    if($vuelto < 0){
                        return ['estado' => 'failed', 'mensaje' => 'La deuda del cliente es '.abs($vuelto).', '];
                    }
                    $venta->vuelto = $vuelto;
                }
                elseif ($datos->forma_pago_id == '1,2'){
                    $venta->forma_pago_id = '1,2';
                    if($datos->debito == 0 || trim($datos->debito)=='' || $datos->efectivo == 0 || trim($datos->efectivo)=='' ){
                        return ['estado'=>'failed', 'mensaje'=>'ingrese el monto en efectivo y/o debito.'];
                    }
                    // $vuelto = ((int)$datos->pago_efectivo + (int)$datos->pago_debito) - (int)$datos->venta_total;
                    $vuelto = $datos->vuelto;
                    if($vuelto < 0){
                        return ['estado' => 'failed', 'mensaje' => 'La deuda del cliente es '.abs($vuelto).', '];
                    }
                    $venta->vuelto = $vuelto;
                }
                elseif ($datos->forma_pago_id == '2,1'){
                    $venta->forma_pago_id = '2,1';
                    if($datos->debito == 0 || trim($datos->debito)=='' || $datos->efectivo == 0 || trim($datos->efectivo)=='' ){
                        return ['estado'=>'failed', 'mensaje'=>'ingrese el monto en efectivo y/o debito.'];
                    }
                    // $vuelto = ((int)$datos->pago_efectivo + (int)$datos->pago_debito) - (int)$datos->venta_total;
                    $vuelto = $datos->vuelto;
                    if($vuelto < 0){
                        return ['estado' => 'failed', 'mensaje' => 'La deuda del cliente es '.abs($vuelto).', '];
                    }
                    $venta->vuelto = $vuelto;
                }
                elseif ($datos->forma_pago_id == '3,undefined') {
                    $venta->forma_pago_id = '3';

                } elseif ($datos->forma_pago_id == 'undefined,undefined') {
                    return ['estado'=>'failed', 'mensaje'=>'seleccione una forma de pago.'];
                } else {
                    $venta->forma_pago_id = $datos->forma_pago_id;
                }

            }

            if($datos->sii_forma_pago == 'CREDITO'){

                //verificar que abono debito o efectivo no sean nulos
                if($datos->efectivo == null || $datos->debito == null){
                    return ['estado'=>'failed', 'mensaje'=>'Abono efectivo o Abono debito sin valores, al menos ingrese un 0'];
                }

                // $deuda = $datos->venta_total - ($datos->pago_efectivo + $datos->pago_debito);
                $deuda = abs($datos->vuelto); // cuando es credito el vuelto pasa hacer la deauda que falta por pagar
                //pago_credito no es el pago como tal, es la deuda ;)
                if($deuda < 0){ // nunca sera negativo la deuda en este caso
                    return ['estado'=>'failed', 'mensaje'=>'El monto de la deuda no puede estar en negativo'];
                }
                $venta->pago_credito = $datos->deuda;
                $venta->detalle_credito = $datos->detalle_credito;
                $venta->forma_pago_id = '3';

            }

            // DE MOMENTO TIPO DE ENTREGA SIN ESPECIFICAR
            // if ($datos->tipo_entrega_id == []) {
            //     return ['estado'=>'failed', 'mensaje'=>'seleccione un tipo de entrega.'];
            // } else {
            //     $venta->tipo_entrega_id = $datos->tipo_entrega_id;
            // }
            $venta->tipo_entrega_id = 3; // SI HAY UN TIPO ENTREGA COMENTAR AQUI
            if ($datos->cliente_id == '') {
                return ['estado'=>'failed', 'mensaje'=>'seleccione un cliente.'];
            } else {
                $venta->cliente_id = $datos->cliente_id;
            }
            //datos totales de factura dte33
            $totales = $datos->totales;
            $venta->totales_impuesto_especifico = $totales['Especifico'];
            $venta->totales_iva = $totales['Iva'];
            $venta->totales_neto = $totales['Neto'];
            $venta->totales_exento = $totales['Exento'];

            if ($venta->save()) {
                $ingresarDetalle = $this->registro_detalle_venta($datos->factura['Productos'], $venta->id);

                $cliente=Cliente::find($datos->cliente_id);

                // dd($ingresarDetalle);
                if ($ingresarDetalle['estado'] == true) {

                    //  DB::commit();
                    $factura = $datos['factura']; // datos que solo quiere el erik
                    $factura['Total'] = $datos['totales']; // datos que solo quiere el erik

                    // si el precio es + iva entonces:
                    if($datos->dte_precio == 'iva_incluido'){ // se descuenta el iva hasta dejar el precio en NETO, erik solo necesita los neto
                        foreach ($factura['Productos'] as $key => &$segment) {
                            $segment['PrecioNeto'] = round($segment['PrecioNeto'] / 1.19);
                            $segment['SubTotal'] = round($segment['SubTotal'] / 1.19); // no se si redondear este valor, puede afectar el monto neto

                        }
                    }

                //    DB::rollback();
                    //ojo aqui EL EMISOR y RECEPTOR
                    $emisor = $datos->factura['emisor'];
                    // dd($emisor['rut']);
                    $receptor = $datos->factura['Cliente'];

                    //para mañana crear metodo de abajo
                    $emisor_reseptor = emisor_receptor_venta::insertar($emisor, $receptor, $venta,$datos->factura['FormaPago_str'], null, null);
                    // dd($receptor['Rut']);

                    if($emisor_reseptor['estado']=='success'){
                        //AQUI NOS VAMOS DEFINITIVAMENTE!!!! A LA API DE DTE (ERIK MILLAR)
                        $pasar_por_api_dte = $this->api_dte($factura);
                        if($pasar_por_api_dte['estado'] == 'success'){

                            $docuemtno_xml = Documentoxml::insertar($venta->id,$pasar_por_api_dte);
                            $documento_xml_envio = Documentoxmlenvio::insertar($venta->id,$pasar_por_api_dte);
                            if($docuemtno_xml == true && $documento_xml_envio == true){
                                DB::commit();
                                return [
                                    'estado' => 'success',
                                    'mensaje' => 'Factura emitida',
                                    'venta' => $venta,
                                    'api_dte' => $pasar_por_api_dte
                                ];
                            }else{
                                DB::rollback();
                                return [
                                    'estado' => 'failed',
                                    'mensaje' => 'Factura NO emitida',
                                    'venta' => null,
                                ];
                            }


                        }else{
                            DB::rollBack();
                            return $pasar_por_api_dte;
                        }
                    }else{
                        DB::rollback();
                        return ['estado'=>'failed','mensaje'=>'Ha ocurrido un fallo con el emisor o receptor'];
                    }
                    // return [
                    //     'venta' =>$venta,
                    //     'detalle_ventas' => $ingresarDetalle['detalle_venta'],
                    //     'factura' => $factura
                    // ];

                } else {
                    if ($ingresarDetalle['estado'] == false) {
                        return ['estado'=>'failed', 'mensaje'=>'Error, la cantidad de venta es mayor al stock.'];
                    }
                    DB::rollBack();
                    return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error, verifique esten correctos los campo.'];
                }
            } else {
                DB::rollBack();
                return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error, verifique esten correctos los campo.'];
            }
    }

    public function registro_detalle_venta($carro, $venta_id)
    {
        // dd($carro);

        for ($i = 0; $i < count($carro); $i++) {
            $productoCantidad = Producto::select([
            'producto.cantidad',
            'producto.id',
            'producto.nombre',
            'producto.stock',
        ])
            ->where('producto.id', $carro[$i]['id'])
            ->where('producto.stock', 'S')
            ->first();
            if ($productoCantidad) {
                if ($carro[$i]['Cantidad'] > $productoCantidad->cantidad) {
                    return ['estado'=>false,'mensaje'=>"No hay Stock en uno de los productos"];
                }
            }
        }
        $count = 0;

        // DB::beginTransaction();
        for ($i = 0; $i < count($carro); $i++) {

            $det_venta = new DetalleVenta;
            $det_venta->user_id = Auth::user()->id;
            $det_venta->venta_id = $venta_id;
            $det_venta->producto_id = $carro[$i]['id'];
            $det_venta->cantidad = $carro[$i]['Cantidad'];
            $det_venta->precio = $carro[$i]['PrecioNeto'];
            //DATOS DE FACTURA (OPCIONALES A LA HORA DE GENERAR DICHA FACTURA)
            $det_venta->descuento = $carro[$i]['DescuentoNeto'];
            $det_venta->porcentaje_descuento = (float)$carro[$i]['PorcentajeDescuentoNeto'];
            $det_venta->impuesto_adicional = $carro[$i]['MontoImpAdicional'];
            $det_venta->tipo_impuesto_adicional = $carro[$i]['TipoImpAdicional'];
            $det_venta->afecto_iva = $carro[$i]['Afecto'];
            $det_venta->unidad = $carro[$i]['UnidadMedida'];

            if ($det_venta->save()) {
                $actualizarCantidad = Producto::find($carro[$i]['id']);

                if ($actualizarCantidad->stock == 'S') {
                    $actualizarCantidad->cantidad = $actualizarCantidad->cantidad - $carro[$i]['Cantidad'];
                    if ($actualizarCantidad->save()) {
                        $count++;
                    }
                } else {
                    $actualizarCantidad->cantidad = null;
                    if ($actualizarCantidad->save()) {
                        $count++;
                    }
                }
            }
        }

        if (count($carro) == $count) {
           // DB::commit();
            return [
                'estado' => true,
                'detalle_venta'=> $det_venta
            ];
        } else {
           // DB::rollBack();
            return ['estado' => false, 'detalle_venta' => []];
        }
    }

    public function consulta_folios(){

        // cargar folios
        $Folios = new \sasco\LibreDTE\Sii\Folios(file_get_contents(__DIR__.'/folio_prueba_neofox.xml'));

        // ejemplos métodos
        echo 'Folios son validos?: ',($Folios->check()?'si':'no'),"\n\n";
        echo 'Rango de folios: ',$Folios->getDesde(),' al ',$Folios->getHasta(),"\n\n";
        if ($Folios->getCaf())
            echo 'CAF: ',$Folios->getCaf()->C14N(),"\n\n";
        echo $Folios->getPrivateKey(),"\n";
        echo $Folios->getPublicKey();
    }

    public function fecha_hora_actual(){

        return ['fecha' => date('d-m-Y'), 'hora' => date('G:i'), 'date'=> date('Y-m-d')];
    }


    public function factura_exenta(){

        // solicitar ambiente desarrollo con configuración
         \sasco\LibreDTE\Sii::setAmbiente(\sasco\LibreDTE\Sii::CERTIFICACION);
        //  \sasco\LibreDTE\Sii::wsdl('CrSeed');
        //  \sasco\LibreDTE\Sii::wsdl('GetTokenFromSeed');
        // primer folio a usar para envio de set de pruebas
        $folios = [
            34 => 25, // factura exenta electrónica + 3 (folios disponibles hasta 100)
            56 => 19, // nota de débito electrónica + 2 (folios disponibles hasta 20)
            61 => 34, // nota de crédito electrónica + 3 (folios disponibles hasta 44)
        ];

        // caratula para el envío de los dte
        $caratula = [
            'RutEnvia' => '77106553-8',
            'RutReceptor' => '18805652-0',
            'FchResol' => '2020-11-09',
            'NroResol' => 0,
        ];

        // datos del emisor
        $Emisor = [
            'RUTEmisor' => '77106553-8',
            'RznSoc' => 'NEOFOX LIMITADA',
            'GiroEmis' => 'Servicios integrales de informática',
            'Acteco' => 726000,
            'DirOrigen' => 'Los Angeles',
            'CmnaOrigen' => 'Los Angeles',
        ];

        $set_pruebas = [
             // CASO 414178-1
            [
                'Encabezado' => [
                    'IdDoc' => [
                        'TipoDTE' => 34,
                        'Folio' => $folios[34],
                    ],
                    'Emisor' => $Emisor,
                    'Receptor' => [
                        'RUTRecep' => '55666777-8',
                        'RznSocRecep' => 'Empresa S.A.',
                        'GiroRecep' => 'Servicios jurídicos',
                        'DirRecep' => 'Santiago',
                        'CmnaRecep' => 'Santiago',
                    ],
                ],
                'Detalle' => [
                    [
                        'NmbItem' => 'HORAS PROGRAMADOR',
                        'QtyItem' => 12,
                        'UnmdItem' => 'Hora',
                        'PrcItem' => 6991,
                    ],
                ],
                'Referencia' => [
                    'TpoDocRef' => 'SET',
                    'FolioRef' => $folios[34],
                    'RazonRef' => 'CASO 414178-1',
                ],
            ],
        ];

        // Objetos de Firma, Folios y EnvioDTE
        $Firma = new \sasco\LibreDTE\FirmaElectronica($this->config['firma']);
        $Folios = [];

        // foreach ($folios as $tipo => $cantidad){
        //     $Folios[$tipo] = new \sasco\LibreDTE\Sii\Folios(file_get_contents('xml/folios/'.$tipo.'.xml'));
            $Folios['34'] = new \sasco\LibreDTE\Sii\Folios(file_get_contents(__DIR__.'/folio_prueba_neofox.xml'));
        // }
        $EnvioDTE = new \sasco\LibreDTE\Sii\EnvioDte();

        // generar cada DTE, timbrar, firmar y agregar al sobre de EnvioDTE

        foreach ($set_pruebas as $documento) {
            $DTE = new \sasco\LibreDTE\Sii\Dte($documento);

            if (!$DTE->timbrar($Folios[$DTE->getTipo()]))
                break;
            if (!$DTE->firmar($Firma))
                break;
            $EnvioDTE->agregar($DTE);
        }

        // enviar dtes y mostrar resultado del envío: track id o bien =false si hubo error
        $EnvioDTE->setCaratula($caratula);

        $EnvioDTE->setFirma($Firma);
        //file_put_contents('xml/EnvioDTE.xml', $EnvioDTE->generar()); // guardar XML en sistema de archivos
        $track_id = $EnvioDTE->enviar();

        // var_dump($track_id);
        return([
         'track_id'=>$track_id,
         '_envio' => $EnvioDTE,
         'set_prueba'=> $set_pruebas,
        //  'response'=>$this->estado_dte_enviado('77106553','8',$track_id)
         ]);

        // si hubo errores mostrar
        foreach (\sasco\LibreDTE\Log::readAll() as $error)
            echo $error,"\n";
    }

    public function estado_dte_enviado(){
        // solicitar token
        $token = \sasco\LibreDTE\Sii\Autenticacion::getToken($this->config['firma']);
        if (!$token) {
            foreach (\sasco\LibreDTE\Log::readAll() as $error)
                echo $error,"\n";
            exit;
        }


        // consultar estado enviado
        $rut = '77106553';
        $dv = '8';
        $trackID = '105813228';

        $estado = \sasco\LibreDTE\Sii::request('QueryEstUp', 'getEstUp', [$rut, $dv, $trackID, $token]);

        // si el estado se pudo recuperar se muestra estado y glosa
        // $peliculas = new \SimpleXMLElement($estado);


        if ($estado!==false) {
            return([
                'consulta'=>[$rut, $dv, $trackID],
                'codigo' => (string)$estado->xpath('/SII:RESPUESTA/SII:RESP_HDR/ESTADO')[0],
                'glosa' => $estado->xpath('/SII:RESPUESTA/SII:RESP_HDR/GLOSA'),
            ]);
        }

        // mostrar error si hubo
        foreach (\sasco\LibreDTE\Log::readAll() as $error)
            echo $error,"\n";
    }

    public function open_factura(){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://dev-api.haulmer.com/v2/dte/document",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>"{\"response\":[\"PDF\",\"FOLIO\"],\"dte\":{\"Encabezado\":{\"IdDoc\":{\"TipoDTE\":33,\"Folio\":0,\"FchEmis\":\"2019-03-18\",\"TpoTranCompra\":\"1\",\"TpoTranVenta\":\"1\",\"FmaPago\":\"2\"},\"Emisor\":{\"RUTEmisor\":\"76795561-8\",\"RznSoc\":\"HAULMER SPA\",\"GiroEmis\":\"VENTA AL POR MENOR POR CORREO, POR INTERNET Y VIA TELEFONICA\",\"Acteco\":\"479100\",\"DirOrigen\":\"ARTURO PRAT 527   CURICO\",\"CmnaOrigen\":\"Curicó\",\"Telefono\":\"0 0\",\"CdgSIISucur\":\"81303347\"},\"Receptor\":{\"RUTRecep\":\"76430498-5\",\"RznSocRecep\":\"HOSTY SPA\",\"GiroRecep\":\"ACTIVIDADES DE CONSULTORIA DE INFORMATIC\",\"DirRecep\":\"ARTURO PRAT 527 3 pis OF 1\",\"CmnaRecep\":\"Curicó\"},\"Totales\":{\"MntNeto\":2000,\"TasaIVA\":\"19\",\"IVA\":380,\"MntTotal\":2380,\"MontoPeriodo\":2380,\"VlrPagar\":2380}},\"Detalle\":[{\"NroLinDet\":1,\"NmbItem\":\"item\",\"QtyItem\":1,\"PrcItem\":2000,\"MontoItem\":2000}]}}\n",
        CURLOPT_HTTPHEADER => array(
            "apikey: 928e15a2d14d4a6292345f04960f4bd3"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }



    //super factura
    public function super_factura(){

        require_once('superfactura-api-php/SuperFacturaAPI/api.php');

        $datos = array(
            'Encabezado' => array(
                'IdDoc' => array(
                    'TipoDTE' => 34,
                    // 'FchEmis' => '2015-01-01', // Opcional
                ),
                'Emisor' => array(
                    'RUTEmisor' => '77106553-8',
                    // Los demás datos serán agregados por SuperFactura
                ),
                'Receptor' => array(
                    'RUTRecep' => '1-9',
                    'RznSocRecep' => 'Test',
                    'GiroRecep' => 'Giro',
                    'DirRecep' => 'Dirección',
                    'CmnaRecep' => 'Comuna',
                    'CiudadRecep' => 'Ciudad',
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

        $api = new SuperFacturaAPI('neofox.informatica@gmail.com', 'Mahpasto2020');

        // $resultado = $api->SendDTE($datos, 'cer'); // 'pro' = ambiente producci�n y 'cer' = ambiente certificaci�n

        $resultado = $api->SendDTE(
            $datos,	// Datos del DTE
            'cer',	// Ambiente: 'pro' = producci�n y 'cer' = certificaci�n
            array(	// El tercer argumento puede contener un arreglo con opciones
                'savePDF' => '/tmp/dte-123',	// Obtiene y guarda el PDF
                'saveXML' => '/tmp/dte-123',	// Obtiene y guarda el XML
            )
        );
        // 3) Procesar salida de la API
        $resultado = $api->SendDTE(
            $datos,	// Datos del DTE
            'cer',	// Ambiente: 'pro' = producci�n y 'cer' = certificaci�n
            array(	// El tercer argumento puede contener un arreglo con opciones
                'getPDF' => true,	// Obtiene el PDF
                 'getXML' => true	// Obtiene el XML
            )
        );

        dd($resultado);
        // 3) Procesar salida de la API

        if($resultado['ok']) {
            // Obtener contenido de archivos PDF
            $pdf = $resultado['pdf'];
            $pdfCedible = $resultado['pdfCedible'];

            echo "Folio: {$resultado['folio']}<br>";

            $size = strlen($pdf);
            echo "PDF: ($size bytes)<br>";

            if($pdfCedible) {
                $sizeCedible = strlen($pdfCedible);
                echo "PDF Cedible: ($sizeCedible bytes)<br>";
            }

            die('ok');

        } else {
            die('Error');
        }
        // return response()->json($api);
    }

    public function documentos_recibidos(){
        require_once('superfactura-api-php/SuperFacturaAPI/api.php');

        $api = new SuperFacturaAPI('neofox.informatica@gmail.com', 'Mahpasto2020');

        $getXML = true; // Usar 'true' para obtener XML completo
        $res = $api->GetDocsRecibidos('77106553-8', '2021-01', $getXML);
        dd($res);

        echo '<pre>';
        foreach($res as $dte) {
            print_r($dte);
        }
    }

    public function super_boleta(){
        require_once('superfactura-api-php/SuperFacturaAPI/api.php');

        $datos = array(
            'Encabezado' => array(
                'IdDoc' => array(
                    'TipoDTE' => 39, // Boleta electr�nica de ventas
                    // 'FchEmis' => '2015-01-01', // Opcional
                    'IndServicio' => 3, // Boletas de venta y servicios
                ),
                'Emisor' => array(
                    'RUTEmisor' => '77106553-8', // Indicar aqu� el RUT de su contribuyente
                    // Los dem�s datos ser�n agregados por SuperFactura
                ),

                'Receptor' => array(
                    'RUTRecep' => '18805652-0',
                    'RznSocRecep' => 'Janucho Godoy',
                    'Contacto' => 'cliente@dominio.cl',
                ),
                // 'Totales' ser� agregado por SuperFactura
            ),
            'Detalles' => array(
                array(
                    // 'NroLinDet' ser� agregado por SuperFactura
                    'NmbItem' => 'koyak el chupete',
                    // 'DscItem' => 'koyak el chupete',
                    'QtyItem' => 1,
                    // 'UnmdItem' => 'KG',
                    'PrcItem' => 1000,
                ),
                array(
                    'NmbItem' => 'cuaderno pre U',
                    'QtyItem' => 1,
                    'PrcItem' => 1000,
                ),
            ),
        );

        // Enviar boleta con la API

        $api = new SuperFacturaAPI('neofox.informatica@gmail.com', 'Mahpasto2020'); // Usar aqu� los datos de su cuenta
        $resultado = $api->SendDTE($datos, 'cer'); // 'pro' = ambiente producci�n y 'cer' = ambiente certificaci�n

        // Procesar salida de la API para obtener el folio asignado.

        if($resultado['ok']) {
            echo "La Boleta #{$resultado['folio']} fue enviada correctamente.<br>";

        } else {
            die('Error');
        }
    }

    public function dte_erik(){ // metodo sin uso, texto parametral ejemplo de uso

        /////////test primero
        $api_dte = $this->api_dte('{
            "Llave": "eTUDwTw$eBj5tChr7$9zf$uZkRqq",
            "Fecha": "2020-12-28 10:00:53",
            "FormaPago": 1,
            "Cliente": {
            "Rut": "15629658-9",
            "RazonSocial": "Erik Millar Bustos",
            "Giro": "personal",
            "Direccion": "alfonso carrasco 228",
            "Comuna": "los angeles",
            "Ciudad": "Los Ángeles"
            },
            "Productos": [
            {
                "Afecto": true,
                "NombreProducto": "Producto Prueba 1",
                "DescripcionAdicional": "",
                "UnidadMedida": "und",
                "Cantidad": 2,
                "PrecioNeto": 100,
                "DescuentoNeto": 0,
                "SubTotal": 200,
                "TipoImpAdicional": 0,
                "MontoImpAdicional": 0
            },
            {
                "Afecto": true,
                "NombreProducto": "Producto Prueba 2",
                "DescripcionAdicional": "Producto con Impuestos especifico",
                "UnidadMedida": "Ltr",
                "Cantidad": 5,
                "PrecioNeto": 312,
                "DescuentoNeto": 0,
                "SubTotal": 1560,
                "TipoImpAdicional": 28,
                "MontoImpAdicional": 371
            }
            ],
            "Total": {
            "Neto": 1760,
            "IVA": 334,
            "Espesifico": 371,
            "Total": 2465
            }
        }');

        return $api_dte;


        ///////////
        // try{
        //     $URL = 'http://webapineofox.millarstic.cl/api/DTE';


        //     $curl = curl_init();

        //     curl_setopt_array($curl, array(
        //     CURLOPT_URL => $URL,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "GET",
        //     CURLOPT_HTTPHEADER => array(
        //         "Content-Type: application/json",

        //     ),
        //     CURLOPT_POSTFIELDS =>'{
        //         "Llave": "eTUDwTw$eBj5tChr7$9zf$uZkRqq",
        //         "Fecha": "2020-12-28 10:00:53",
        //         "FormaPago": 1,
        //         "Cliente": {
        //         "Rut": "15629658-9",
        //         "RazonSocial": "Erik Millar Bustos",
        //         "Giro": "personal",
        //         "Direccion": "alfonso carrasco 228",
        //         "Comuna": "los angeles",
        //         "Ciudad": "Los Ángeles"
        //         },
        //         "Productos": [
        //         {
        //             "Afecto": true,
        //             "NombreProducto": "Producto Prueba 1",
        //             "DescripcionAdicional": "",
        //             "UnidadMedida": "und",
        //             "Cantidad": 2,
        //             "PrecioNeto": 100,
        //             "DescuentoNeto": 0,
        //             "SubTotal": 200,
        //             "TipoImpAdicional": 0,
        //             "MontoImpAdicional": 0
        //         },
        //         {
        //             "Afecto": true,
        //             "NombreProducto": "Producto Prueba 2",
        //             "DescripcionAdicional": "Producto con Impuestos especifico",
        //             "UnidadMedida": "Ltr",
        //             "Cantidad": 5,
        //             "PrecioNeto": 312,
        //             "DescuentoNeto": 0,
        //             "SubTotal": 1560,
        //             "TipoImpAdicional": 28,
        //             "MontoImpAdicional": 371
        //         }
        //         ],
        //         "Total": {
        //         "Neto": 1760,
        //         "IVA": 334,
        //         "Espesifico": 371,
        //         "Total": 2465
        //         }
        //     }'

        //     ));

        //     $response = curl_exec($curl);
        //     curl_close($curl);

        //     $json_legal = json_decode($response);

        //     $decode_dte = base64_decode($json_legal->DTE);
        //     $ted = $this->fragmentar_xml($decode_dte, 'TED');

        //     return [
        //         'estado' => 'success',
        //         'dte' => $decode_dte,
        //         'ted' => $ted,
        //     ];


        // }catch(QueryException $e){
		// 	return [
        //         'estado' => 'failed',
        //         'qex' => $e
        //     ];
        // }

		// catch(\Exception $e){
        //     return [
        //         'estado' => 'failed',
        //         'qex' => $e
        //     ];
		// }



    }

    public function api_dte($json_factura){ //metodo definitivo a usar, descomentar curl para testear a api de erik
        try{
            // $URL = 'http://webapineofox.millarstic.cl/api/DTE';


            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            // CURLOPT_URL => $URL,
            // CURLOPT_RETURNTRANSFER => true,
            // CURLOPT_ENCODING => "",
            // CURLOPT_MAXREDIRS => 10,
            // CURLOPT_TIMEOUT => 0,
            // CURLOPT_FOLLOWLOCATION => true,
            // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            // CURLOPT_CUSTOMREQUEST => "GET",
            // CURLOPT_HTTPHEADER => array(
            //     "Content-Type: application/json",

            // ),
            // CURLOPT_POSTFIELDS =>"$json_factura"

            // ));

            // $response = curl_exec($curl);
            // curl_close($curl);

            // $json_legal = json_decode($response);

            // $decode_dte = base64_decode($json_legal->DTE);
            // return [
            //     'json' => $json_legal,
            //     'decode' => $decode_dte
            // ];


            $decode_dte = base64_decode("PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPERURSB2ZXJzaW9uPSIxLjAiPg0KPERvY3VtZW50byBJRD0iTVNUSUM3NzAzMzQ2MS02MzM2MjAyMDEyMjgxMDAwNTMiPg0KPEVuY2FiZXphZG8+DQo8SWREb2M+DQo8VGlwb0RURT4zMzwvVGlwb0RURT4NCjxGb2xpbz42PC9Gb2xpbz4NCjxGY2hFbWlzPjIwMjAtMTItMjg8L0ZjaEVtaXM+DQo8Rm1hUGFnbz4xPC9GbWFQYWdvPg0KPC9JZERvYz4NCjxFbWlzb3I+DQo8UlVURW1pc29yPjc3MDMzNDYxLTY8L1JVVEVtaXNvcj4NCjxSem5Tb2M+U0VSVklDSU9TIFRJQyBFUklLIEFMRVhJUyBNSUxMQVIgQlVTVE9TIEUuSS5SLkwuPC9Sem5Tb2M+DQo8R2lyb0VtaXM+QUNUSVZJREFERVMgREUgQ09OU1VMVE9SSUEgREUgSU5GT1JNQVRJQ0EgWSBERSBHRVNUSU9OIERFIElOU1RBTEFDSU9ORTwvR2lyb0VtaXM+DQo8QWN0ZWNvPjYyMDIwMDwvQWN0ZWNvPg0KPEFjdGVjbz42MjA5MDA8L0FjdGVjbz4NCjxEaXJPcmlnZW4+QUxGT05TTyBDQVJSQVNDTyAyODg8L0Rpck9yaWdlbj4NCjxDbW5hT3JpZ2VuPkxPUyBBTkdFTEVTPC9DbW5hT3JpZ2VuPg0KPC9FbWlzb3I+DQo8UmVjZXB0b3I+DQo8UlVUUmVjZXA+MTU2Mjk2NTgtOTwvUlVUUmVjZXA+DQo8UnpuU29jUmVjZXA+RXJpayBNaWxsYXIgQnVzdG9zPC9Sem5Tb2NSZWNlcD4NCjxHaXJvUmVjZXA+cGVyc29uYWw8L0dpcm9SZWNlcD4NCjxEaXJSZWNlcD5hbGZvbnNvIGNhcnJhc2NvIDIyODwvRGlyUmVjZXA+DQo8Q21uYVJlY2VwPmxvcyBhbmdlbGVzPC9DbW5hUmVjZXA+DQo8Q2l1ZGFkUmVjZXA+TG9zIO+/vW5nZWxlczwvQ2l1ZGFkUmVjZXA+DQo8L1JlY2VwdG9yPg0KPFRvdGFsZXM+DQo8TW50TmV0bz4xNzYwPC9NbnROZXRvPg0KPFRhc2FJVkE+MTk8L1Rhc2FJVkE+DQo8SVZBPjMzNDwvSVZBPg0KPEltcHRvUmV0ZW4+DQo8VGlwb0ltcD4yODwvVGlwb0ltcD4NCjxUYXNhSW1wPjAuMTU8L1Rhc2FJbXA+DQo8TW9udG9JbXA+MzcxPC9Nb250b0ltcD4NCjwvSW1wdG9SZXRlbj4NCjxNbnRUb3RhbD4yNDY1PC9NbnRUb3RhbD4NCjwvVG90YWxlcz4NCjwvRW5jYWJlemFkbz4NCjxEZXRhbGxlPg0KPE5yb0xpbkRldD4xPC9Ocm9MaW5EZXQ+DQo8Tm1iSXRlbT5Qcm9kdWN0byBQcnVlYmEgMTwvTm1iSXRlbT4NCjxRdHlJdGVtPjI8L1F0eUl0ZW0+DQo8UHJjSXRlbT4xMDA8L1ByY0l0ZW0+DQo8TW9udG9JdGVtPjIwMDwvTW9udG9JdGVtPg0KPC9EZXRhbGxlPg0KPERldGFsbGU+DQo8TnJvTGluRGV0PjI8L05yb0xpbkRldD4NCjxObWJJdGVtPlByb2R1Y3RvIFBydWViYSAyPC9ObWJJdGVtPg0KPERzY0l0ZW0+UHJvZHVjdG8gY29uIEltcHVlc3RvcyBlc3BlY2lmaWNvPC9Ec2NJdGVtPg0KPFF0eUl0ZW0+NTwvUXR5SXRlbT4NCjxQcmNJdGVtPjMxMjwvUHJjSXRlbT4NCjxDb2RJbXBBZGljPjI4PC9Db2RJbXBBZGljPg0KPE1vbnRvSXRlbT4xNTYwPC9Nb250b0l0ZW0+DQo8L0RldGFsbGU+DQo8VEVEIHZlcnNpb249IjEuMCI+DQo8REQ+DQo8UkU+NzcwMzM0NjEtNjwvUkU+DQo8VEQ+MzM8L1REPg0KPEY+NjwvRj4NCjxGRT4yMDIwLTEyLTI4PC9GRT4NCjxSUj4xNTYyOTY1OC05PC9SUj4NCjxSU1I+RXJpayBNaWxsYXIgQnVzdG9zPC9SU1I+DQo8TU5UPjI0NjU8L01OVD4NCjxJVDE+UHJvZHVjdG8gUHJ1ZWJhIDE8L0lUMT4NCjxDQUYgdmVyc2lvbj0iMS4wIj4NCjxEQT4NCjxSRT43NzAzMzQ2MS02PC9SRT4NCjxSUz5TRVJWSUNJT1MgVElDIEVSSUsgQUxFWElTIE1JTExBUiBCVVNUT1M8L1JTPg0KPFREPjMzPC9URD4NCjxSTkc+DQo8RD4xPC9EPg0KPEg+MTA8L0g+DQo8L1JORz4NCjxGQT4yMDIwLTEyLTI3PC9GQT4NCjxSU0FQSz4NCjxNPnFoNDMxS1JwTjNhREVIdmtCZTBOZWN2WkRQVXJGVy9TVlkreHpEMGRXVDI3WGhGTE5PWm1MSmp2NzVsK0FpdDlJNXpML2ZBeE5JWHdxeWRPR3hOKzB3PT08L00+DQo8RT5Bdz09PC9FPg0KPC9SU0FQSz4NCjxJREs+MTAwPC9JREs+DQo8L0RBPg0KPEZSTUEgYWxnb3JpdG1vPSJTSEExd2l0aFJTQSI+T1FQNFRUbDNlUnVOcEdoWTBkMGl1aWpka0ptc2t2eGgwVXlUaUIxZUpiUjBaZzRuUEhGeHN5ZHZJYjNyb3VwbE40bWxLZ2lGOEIvS3VpYnc0MHpyY2c9PTwvRlJNQT4NCjwvQ0FGPg0KPFRTVEVEPjIwMjEtMDEtMTVUMTQ6MzE6MTQ8L1RTVEVEPg0KPC9ERD4NCjxGUk1UIGFsZ29yaXRtbz0iU0hBMXdpdGhSU0EiPmR6NGpPR0hwR0FlRXdPRWR3dUY5NkJ2U05CUmJwQjJER3I2OWY4NzFadnJUdndValJzOGNwVmFMRHF6RGFoTkFZVFNaVFBhalF3Y3NXVmJad1dicEpRPT08L0ZSTVQ+DQo8L1RFRD4NCjxUbXN0RmlybWE+MjAyMS0wMS0xNVQxNDozMToxNDwvVG1zdEZpcm1hPg0KPC9Eb2N1bWVudG8+DQo8U2lnbmF0dXJlIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjIj48U2lnbmVkSW5mbz48Q2Fub25pY2FsaXphdGlvbk1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnL1RSLzIwMDEvUkVDLXhtbC1jMTRuLTIwMDEwMzE1IiAvPjxTaWduYXR1cmVNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjcnNhLXNoYTEiIC8+PFJlZmVyZW5jZSBVUkk9IiNNU1RJQzc3MDMzNDYxLTYzMzYyMDIwMTIyODEwMDA1MyI+PFRyYW5zZm9ybXM+PFRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnL1RSLzIwMDEvUkVDLXhtbC1jMTRuLTIwMDEwMzE1IiAvPjwvVHJhbnNmb3Jtcz48RGlnZXN0TWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI3NoYTEiIC8+PERpZ2VzdFZhbHVlPmJoZFZ2dER2MXBrK2VFbGpmTzVIOThXU0dTcz08L0RpZ2VzdFZhbHVlPjwvUmVmZXJlbmNlPjwvU2lnbmVkSW5mbz48U2lnbmF0dXJlVmFsdWU+UDhOWEF3aW8zbnp0U0NxRTRicklUTHNFbmN1OXpSblVHUzhLSmJ5ajYzZnM4cWEzY3ovMGErKzFDbEl2UmdkNUl2UVE1UDY1dmtscEdPNU9oeVMwYW83ZldmYjdFbW55YnpYVC9uSnlWQngzV1hWWTlFUERha1lNbXdmOUlBRjJ3bjBFMERxTlZHYW0vOXZkb0RFdkUvU1o5ZnZLb2d5dXpSRDV3SjQ4T3pNPTwvU2lnbmF0dXJlVmFsdWU+PEtleUluZm8+PEtleVZhbHVlPjxSU0FLZXlWYWx1ZT48TW9kdWx1cz5ob3NqckVBcDJsczg4RU5QM05HRVhiWktrV01uclUzdWRXaDA0dGVDSUxXcm51bFRJVkcxSUhJVzcvV0o4U20zd2dyblpWb3hGWFN0OHg4YUhsSUptK1VCWFdQTVJuUTcrT3V4WTlzT1hPTzV2SjAxSytNVTluMDVIemZTbjFMWVJVZ3ZTUXNHcllnaW5yNkNDeFdmc3FxOTFEMXlyNnh0WGhnd3ZneUhaSmM9PC9Nb2R1bHVzPjxFeHBvbmVudD5BUUFCPC9FeHBvbmVudD48L1JTQUtleVZhbHVlPjwvS2V5VmFsdWU+PFg1MDlEYXRhPjxYNTA5Q2VydGlmaWNhdGU+TUlJR0x6Q0NCUmVnQXdJQkFnSUtIa0xrc3dBQUFCTjh1VEFOQmdrcWhraUc5dzBCQVFVRkFEQ0IwakVMTUFrR0ExVUVCaE1DUTB3eEhUQWJCZ05WQkFnVEZGSmxaMmx2YmlCTlpYUnliM0J2YkdsMFlXNWhNUkV3RHdZRFZRUUhFd2hUWVc1MGFXRm5iekVVTUJJR0ExVUVDaE1MUlMxRFJWSlVRMGhKVEVVeElEQWVCZ05WQkFzVEYwRjFkRzl5YVdSaFpDQkRaWEowYVdacFkyRmtiM0poTVRBd0xnWURWUVFERXlkRkxVTkZVbFJEU0VsTVJTQkRRU0JHU1ZKTlFTQkZURVZEVkZKUFRrbERRU0JUU1UxUVRFVXhKekFsQmdrcWhraUc5dzBCQ1FFV0dITmpiR2xsYm5SbGMwQmxMV05sY25SamFHbHNaUzVqYkRBZUZ3MHlNVEF4TURreE5qSTBOVFJhRncweU5EQXhNRGt4TmpJME5UUmFNSUd3TVFzd0NRWURWUVFHRXdKRFRERVFNQTRHQTFVRUNBd0hRa2xQUXNPTlR6RVFNQTRHQTFVRUJ3d0hRa2xQUXNPTlR6RWpNQ0VHQTFVRUNoTWFSa2xFUlV3Z1FWSk9UMHhFVHlCSlUweEJJRWRCVWtOSlFTQXhDakFJQmdOVkJBc01BU294SWpBZ0JnTlZCQU1UR1VaSlJFVk1JRUZTVGs5TVJFOGdTVk5NUVNCSFFWSkRTVUV4S0RBbUJna3Foa2lHOXcwQkNRRVdHVXhKVGtSQlRrRXVSbFZGVGxSRlUwQkhUVUZKVEM1RFQwMHdnWjh3RFFZSktvWklodmNOQVFFQkJRQURnWTBBTUlHSkFvR0JBSWFMSTZ4QUtkcGJQUEJEVDl6UmhGMjJTcEZqSjYxTjduVm9kT0xYZ2lDMXE1N3BVeUZSdFNCeUZ1LzFpZkVwdDhJSzUyVmFNUlYwcmZNZkdoNVNDWnZsQVYxanpFWjBPL2pyc1dQYkRsemp1YnlkTlN2akZQWjlPUjgzMHA5UzJFVklMMGtMQnEySUlwNitnZ3NWbjdLcXZkUTljcStzYlY0WU1MNE1oMlNYQWdNQkFBR2pnZ0twTUlJQ3BUQ0NBVThHQTFVZElBU0NBVVl3Z2dGQ01JSUJQZ1lJS3dZQkJBSERVZ1V3Z2dFd01DMEdDQ3NHQVFVRkJ3SUJGaUZvZEhSd09pOHZkM2QzTG1VdFkyVnlkR05vYVd4bExtTnNMME5RVXk1b2RHMHdnZjRHQ0NzR0FRVUZCd0lDTUlIeEhvSHVBRVVBYkFBZ0FISUFaUUJ6QUhBQWJ3QnVBR1FBWlFCeUFDQUFaUUJ6QUhRQVpRQWdBR1lBYndCeUFHMEFkUUJzQUdFQWNnQnBBRzhBSUFCbEFITUFJQUIxQUc0QUlBQnlBR1VBY1FCMUFHa0Fjd0JwQUhRQWJ3QWdBR2tBYmdCa0FHa0Fjd0J3QUdVQWJnQnpBR0VBWWdCc0FHVUFJQUJ3QUdFQWNnQmhBQ0FBWkFCaEFISUFJQUJwQUc0QWFRQmpBR2tBYndBZ0FHRUFiQUFnQUhBQWNnQnZBR01BWlFCekFHOEFJQUJrQUdVQUlBQmpBR1VBY2dCMEFHa0FaZ0JwQUdNQVlRQmpBR2tBOHdCdUFDNEFJQUJRQUc4QWN3QjBBR1VBY2dCcEFHOEFjZ0J0QUdVQWJnQjBBR1VBTERBZEJnTlZIUTRFRmdRVVp0L2ZlL2hUdVN2YmgreXQ5NTFBT0FFUVZMSXdDd1lEVlIwUEJBUURBZ1R3TUNNR0ExVWRFUVFjTUJxZ0dBWUlLd1lCQkFIQkFRR2dEQllLTURrMU5qRXpOREF0TXpBZkJnTlZIU01FR0RBV2dCUjQ0VDZmMGhLemVqeU56VEFPVTdOREtRZXpWVEErQmdOVkhSOEVOekExTURPZ01hQXZoaTFvZEhSd09pOHZZM0pzTG1VdFkyVnlkR05vYVd4bExtTnNMMlZqWlhKMFkyaHBiR1ZqWVVaRlV5NWpjbXd3T2dZSUt3WUJCUVVIQVFFRUxqQXNNQ29HQ0NzR0FRVUZCekFCaGg1b2RIUndPaTh2YjJOemNDNWxZMlZ5ZEdOb2FXeGxMbU5zTDI5amMzQXdQUVlKS3dZQkJBR0NOeFVIQkRBd0xnWW1Ld1lCQkFHQ054VUlndHlETDRXVGpHYUYxWjBYZ3VMY0o0SHY3RHhoZ2N1ZUZJYW9nbGdDQVdRQ0FRUXdJd1lEVlIwU0JCd3dHcUFZQmdnckJnRUVBY0VCQXFBTUZnbzVOamt5T0RFNE1DMDFNQTBHQ1NxR1NJYjNEUUVCQlFVQUE0SUJBUUN6MnlQeGhzcDlaZFFBV2pFd3lydkQ3eUcyc0pod3RMOU9mUnhjcEh6RzkxUnBqK3pRdGJBT0U5WkQ5L21ScnhVMTBGUmNEN095RUtuSFFUUDg4ZG5MYW9wWEppMU1iL2pkcTgvRE16S0RIazI1Sy9qbEJqS0QwNmlRcExxKzZlb0dUUjNYV2YzVjhBOXkxb2FIUnFrb3R2VThoSGY2SlNnR2cyaFYwU2UrSFptdG5ia2d4dHRpY01mQUg4TnNFdkVMVWpPc2lXYitxZkZaVlAwR2RxZVBuZmM0RmdENnZyVXkxaDFWSjhCRzQzL2lCNUZNVkQ0bk4ycWp3YkFYUUMwNGJ6VUhsYzBpQkQxZEd5SWcyVzQ0Z1hRTU9JMTNXU0FBRSs3ZVB1SEgzeGNtR29EbnZlRnVsV0pFK1RWYUxINnZvNFJQYW1UTithT3RUclZseWh3djwvWDUwOUNlcnRpZmljYXRlPjwvWDUwOURhdGE+PC9LZXlJbmZvPjwvU2lnbmF0dXJlPjwvRFRFPg==");
            $decode_envio=base64_decode("PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPERURSB2ZXJzaW9uPSIxLjAiPg0KPERvY3VtZW50byBJRD0iTVNUSUM3NzAzMzQ2MS02MzM2MjAyMDEyMjgxMDAwNTMiPg0KPEVuY2FiZXphZG8+DQo8SWREb2M+DQo8VGlwb0RURT4zMzwvVGlwb0RURT4NCjxGb2xpbz42PC9Gb2xpbz4NCjxGY2hFbWlzPjIwMjAtMTItMjg8L0ZjaEVtaXM+DQo8Rm1hUGFnbz4xPC9GbWFQYWdvPg0KPC9JZERvYz4NCjxFbWlzb3I+DQo8UlVURW1pc29yPjc3MDMzNDYxLTY8L1JVVEVtaXNvcj4NCjxSem5Tb2M+U0VSVklDSU9TIFRJQyBFUklLIEFMRVhJUyBNSUxMQVIgQlVTVE9TIEUuSS5SLkwuPC9Sem5Tb2M+DQo8R2lyb0VtaXM+QUNUSVZJREFERVMgREUgQ09OU1VMVE9SSUEgREUgSU5GT1JNQVRJQ0EgWSBERSBHRVNUSU9OIERFIElOU1RBTEFDSU9ORTwvR2lyb0VtaXM+DQo8QWN0ZWNvPjYyMDIwMDwvQWN0ZWNvPg0KPEFjdGVjbz42MjA5MDA8L0FjdGVjbz4NCjxEaXJPcmlnZW4+QUxGT05TTyBDQVJSQVNDTyAyODg8L0Rpck9yaWdlbj4NCjxDbW5hT3JpZ2VuPkxPUyBBTkdFTEVTPC9DbW5hT3JpZ2VuPg0KPC9FbWlzb3I+DQo8UmVjZXB0b3I+DQo8UlVUUmVjZXA+MTU2Mjk2NTgtOTwvUlVUUmVjZXA+DQo8UnpuU29jUmVjZXA+RXJpayBNaWxsYXIgQnVzdG9zPC9Sem5Tb2NSZWNlcD4NCjxHaXJvUmVjZXA+cGVyc29uYWw8L0dpcm9SZWNlcD4NCjxEaXJSZWNlcD5hbGZvbnNvIGNhcnJhc2NvIDIyODwvRGlyUmVjZXA+DQo8Q21uYVJlY2VwPmxvcyBhbmdlbGVzPC9DbW5hUmVjZXA+DQo8Q2l1ZGFkUmVjZXA+TG9zIO+/vW5nZWxlczwvQ2l1ZGFkUmVjZXA+DQo8L1JlY2VwdG9yPg0KPFRvdGFsZXM+DQo8TW50TmV0bz4xNzYwPC9NbnROZXRvPg0KPFRhc2FJVkE+MTk8L1Rhc2FJVkE+DQo8SVZBPjMzNDwvSVZBPg0KPEltcHRvUmV0ZW4+DQo8VGlwb0ltcD4yODwvVGlwb0ltcD4NCjxUYXNhSW1wPjAuMTU8L1Rhc2FJbXA+DQo8TW9udG9JbXA+MzcxPC9Nb250b0ltcD4NCjwvSW1wdG9SZXRlbj4NCjxNbnRUb3RhbD4yNDY1PC9NbnRUb3RhbD4NCjwvVG90YWxlcz4NCjwvRW5jYWJlemFkbz4NCjxEZXRhbGxlPg0KPE5yb0xpbkRldD4xPC9Ocm9MaW5EZXQ+DQo8Tm1iSXRlbT5Qcm9kdWN0byBQcnVlYmEgMTwvTm1iSXRlbT4NCjxRdHlJdGVtPjI8L1F0eUl0ZW0+DQo8UHJjSXRlbT4xMDA8L1ByY0l0ZW0+DQo8TW9udG9JdGVtPjIwMDwvTW9udG9JdGVtPg0KPC9EZXRhbGxlPg0KPERldGFsbGU+DQo8TnJvTGluRGV0PjI8L05yb0xpbkRldD4NCjxObWJJdGVtPlByb2R1Y3RvIFBydWViYSAyPC9ObWJJdGVtPg0KPERzY0l0ZW0+UHJvZHVjdG8gY29uIEltcHVlc3RvcyBlc3BlY2lmaWNvPC9Ec2NJdGVtPg0KPFF0eUl0ZW0+NTwvUXR5SXRlbT4NCjxQcmNJdGVtPjMxMjwvUHJjSXRlbT4NCjxDb2RJbXBBZGljPjI4PC9Db2RJbXBBZGljPg0KPE1vbnRvSXRlbT4xNTYwPC9Nb250b0l0ZW0+DQo8L0RldGFsbGU+DQo8VEVEIHZlcnNpb249IjEuMCI+DQo8REQ+DQo8UkU+NzcwMzM0NjEtNjwvUkU+DQo8VEQ+MzM8L1REPg0KPEY+NjwvRj4NCjxGRT4yMDIwLTEyLTI4PC9GRT4NCjxSUj4xNTYyOTY1OC05PC9SUj4NCjxSU1I+RXJpayBNaWxsYXIgQnVzdG9zPC9SU1I+DQo8TU5UPjI0NjU8L01OVD4NCjxJVDE+UHJvZHVjdG8gUHJ1ZWJhIDE8L0lUMT4NCjxDQUYgdmVyc2lvbj0iMS4wIj4NCjxEQT4NCjxSRT43NzAzMzQ2MS02PC9SRT4NCjxSUz5TRVJWSUNJT1MgVElDIEVSSUsgQUxFWElTIE1JTExBUiBCVVNUT1M8L1JTPg0KPFREPjMzPC9URD4NCjxSTkc+DQo8RD4xPC9EPg0KPEg+MTA8L0g+DQo8L1JORz4NCjxGQT4yMDIwLTEyLTI3PC9GQT4NCjxSU0FQSz4NCjxNPnFoNDMxS1JwTjNhREVIdmtCZTBOZWN2WkRQVXJGVy9TVlkreHpEMGRXVDI3WGhGTE5PWm1MSmp2NzVsK0FpdDlJNXpML2ZBeE5JWHdxeWRPR3hOKzB3PT08L00+DQo8RT5Bdz09PC9FPg0KPC9SU0FQSz4NCjxJREs+MTAwPC9JREs+DQo8L0RBPg0KPEZSTUEgYWxnb3JpdG1vPSJTSEExd2l0aFJTQSI+T1FQNFRUbDNlUnVOcEdoWTBkMGl1aWpka0ptc2t2eGgwVXlUaUIxZUpiUjBaZzRuUEhGeHN5ZHZJYjNyb3VwbE40bWxLZ2lGOEIvS3VpYnc0MHpyY2c9PTwvRlJNQT4NCjwvQ0FGPg0KPFRTVEVEPjIwMjEtMDEtMTVUMTQ6MzE6MTQ8L1RTVEVEPg0KPC9ERD4NCjxGUk1UIGFsZ29yaXRtbz0iU0hBMXdpdGhSU0EiPmR6NGpPR0hwR0FlRXdPRWR3dUY5NkJ2U05CUmJwQjJER3I2OWY4NzFadnJUdndValJzOGNwVmFMRHF6RGFoTkFZVFNaVFBhalF3Y3NXVmJad1dicEpRPT08L0ZSTVQ+DQo8L1RFRD4NCjxUbXN0RmlybWE+MjAyMS0wMS0xNVQxNDozMToxNDwvVG1zdEZpcm1hPg0KPC9Eb2N1bWVudG8+DQo8U2lnbmF0dXJlIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjIj48U2lnbmVkSW5mbz48Q2Fub25pY2FsaXphdGlvbk1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnL1RSLzIwMDEvUkVDLXhtbC1jMTRuLTIwMDEwMzE1IiAvPjxTaWduYXR1cmVNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjcnNhLXNoYTEiIC8+PFJlZmVyZW5jZSBVUkk9IiNNU1RJQzc3MDMzNDYxLTYzMzYyMDIwMTIyODEwMDA1MyI+PFRyYW5zZm9ybXM+PFRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnL1RSLzIwMDEvUkVDLXhtbC1jMTRuLTIwMDEwMzE1IiAvPjwvVHJhbnNmb3Jtcz48RGlnZXN0TWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI3NoYTEiIC8+PERpZ2VzdFZhbHVlPmJoZFZ2dER2MXBrK2VFbGpmTzVIOThXU0dTcz08L0RpZ2VzdFZhbHVlPjwvUmVmZXJlbmNlPjwvU2lnbmVkSW5mbz48U2lnbmF0dXJlVmFsdWU+UDhOWEF3aW8zbnp0U0NxRTRicklUTHNFbmN1OXpSblVHUzhLSmJ5ajYzZnM4cWEzY3ovMGErKzFDbEl2UmdkNUl2UVE1UDY1dmtscEdPNU9oeVMwYW83ZldmYjdFbW55YnpYVC9uSnlWQngzV1hWWTlFUERha1lNbXdmOUlBRjJ3bjBFMERxTlZHYW0vOXZkb0RFdkUvU1o5ZnZLb2d5dXpSRDV3SjQ4T3pNPTwvU2lnbmF0dXJlVmFsdWU+PEtleUluZm8+PEtleVZhbHVlPjxSU0FLZXlWYWx1ZT48TW9kdWx1cz5ob3NqckVBcDJsczg4RU5QM05HRVhiWktrV01uclUzdWRXaDA0dGVDSUxXcm51bFRJVkcxSUhJVzcvV0o4U20zd2dyblpWb3hGWFN0OHg4YUhsSUptK1VCWFdQTVJuUTcrT3V4WTlzT1hPTzV2SjAxSytNVTluMDVIemZTbjFMWVJVZ3ZTUXNHcllnaW5yNkNDeFdmc3FxOTFEMXlyNnh0WGhnd3ZneUhaSmM9PC9Nb2R1bHVzPjxFeHBvbmVudD5BUUFCPC9FeHBvbmVudD48L1JTQUtleVZhbHVlPjwvS2V5VmFsdWU+PFg1MDlEYXRhPjxYNTA5Q2VydGlmaWNhdGU+TUlJR0x6Q0NCUmVnQXdJQkFnSUtIa0xrc3dBQUFCTjh1VEFOQmdrcWhraUc5dzBCQVFVRkFEQ0IwakVMTUFrR0ExVUVCaE1DUTB3eEhUQWJCZ05WQkFnVEZGSmxaMmx2YmlCTlpYUnliM0J2YkdsMFlXNWhNUkV3RHdZRFZRUUhFd2hUWVc1MGFXRm5iekVVTUJJR0ExVUVDaE1MUlMxRFJWSlVRMGhKVEVVeElEQWVCZ05WQkFzVEYwRjFkRzl5YVdSaFpDQkRaWEowYVdacFkyRmtiM0poTVRBd0xnWURWUVFERXlkRkxVTkZVbFJEU0VsTVJTQkRRU0JHU1ZKTlFTQkZURVZEVkZKUFRrbERRU0JUU1UxUVRFVXhKekFsQmdrcWhraUc5dzBCQ1FFV0dITmpiR2xsYm5SbGMwQmxMV05sY25SamFHbHNaUzVqYkRBZUZ3MHlNVEF4TURreE5qSTBOVFJhRncweU5EQXhNRGt4TmpJME5UUmFNSUd3TVFzd0NRWURWUVFHRXdKRFRERVFNQTRHQTFVRUNBd0hRa2xQUXNPTlR6RVFNQTRHQTFVRUJ3d0hRa2xQUXNPTlR6RWpNQ0VHQTFVRUNoTWFSa2xFUlV3Z1FWSk9UMHhFVHlCSlUweEJJRWRCVWtOSlFTQXhDakFJQmdOVkJBc01BU294SWpBZ0JnTlZCQU1UR1VaSlJFVk1JRUZTVGs5TVJFOGdTVk5NUVNCSFFWSkRTVUV4S0RBbUJna3Foa2lHOXcwQkNRRVdHVXhKVGtSQlRrRXVSbFZGVGxSRlUwQkhUVUZKVEM1RFQwMHdnWjh3RFFZSktvWklodmNOQVFFQkJRQURnWTBBTUlHSkFvR0JBSWFMSTZ4QUtkcGJQUEJEVDl6UmhGMjJTcEZqSjYxTjduVm9kT0xYZ2lDMXE1N3BVeUZSdFNCeUZ1LzFpZkVwdDhJSzUyVmFNUlYwcmZNZkdoNVNDWnZsQVYxanpFWjBPL2pyc1dQYkRsemp1YnlkTlN2akZQWjlPUjgzMHA5UzJFVklMMGtMQnEySUlwNitnZ3NWbjdLcXZkUTljcStzYlY0WU1MNE1oMlNYQWdNQkFBR2pnZ0twTUlJQ3BUQ0NBVThHQTFVZElBU0NBVVl3Z2dGQ01JSUJQZ1lJS3dZQkJBSERVZ1V3Z2dFd01DMEdDQ3NHQVFVRkJ3SUJGaUZvZEhSd09pOHZkM2QzTG1VdFkyVnlkR05vYVd4bExtTnNMME5RVXk1b2RHMHdnZjRHQ0NzR0FRVUZCd0lDTUlIeEhvSHVBRVVBYkFBZ0FISUFaUUJ6QUhBQWJ3QnVBR1FBWlFCeUFDQUFaUUJ6QUhRQVpRQWdBR1lBYndCeUFHMEFkUUJzQUdFQWNnQnBBRzhBSUFCbEFITUFJQUIxQUc0QUlBQnlBR1VBY1FCMUFHa0Fjd0JwQUhRQWJ3QWdBR2tBYmdCa0FHa0Fjd0J3QUdVQWJnQnpBR0VBWWdCc0FHVUFJQUJ3QUdFQWNnQmhBQ0FBWkFCaEFISUFJQUJwQUc0QWFRQmpBR2tBYndBZ0FHRUFiQUFnQUhBQWNnQnZBR01BWlFCekFHOEFJQUJrQUdVQUlBQmpBR1VBY2dCMEFHa0FaZ0JwQUdNQVlRQmpBR2tBOHdCdUFDNEFJQUJRQUc4QWN3QjBBR1VBY2dCcEFHOEFjZ0J0QUdVQWJnQjBBR1VBTERBZEJnTlZIUTRFRmdRVVp0L2ZlL2hUdVN2YmgreXQ5NTFBT0FFUVZMSXdDd1lEVlIwUEJBUURBZ1R3TUNNR0ExVWRFUVFjTUJxZ0dBWUlLd1lCQkFIQkFRR2dEQllLTURrMU5qRXpOREF0TXpBZkJnTlZIU01FR0RBV2dCUjQ0VDZmMGhLemVqeU56VEFPVTdOREtRZXpWVEErQmdOVkhSOEVOekExTURPZ01hQXZoaTFvZEhSd09pOHZZM0pzTG1VdFkyVnlkR05vYVd4bExtTnNMMlZqWlhKMFkyaHBiR1ZqWVVaRlV5NWpjbXd3T2dZSUt3WUJCUVVIQVFFRUxqQXNNQ29HQ0NzR0FRVUZCekFCaGg1b2RIUndPaTh2YjJOemNDNWxZMlZ5ZEdOb2FXeGxMbU5zTDI5amMzQXdQUVlKS3dZQkJBR0NOeFVIQkRBd0xnWW1Ld1lCQkFHQ054VUlndHlETDRXVGpHYUYxWjBYZ3VMY0o0SHY3RHhoZ2N1ZUZJYW9nbGdDQVdRQ0FRUXdJd1lEVlIwU0JCd3dHcUFZQmdnckJnRUVBY0VCQXFBTUZnbzVOamt5T0RFNE1DMDFNQTBHQ1NxR1NJYjNEUUVCQlFVQUE0SUJBUUN6MnlQeGhzcDlaZFFBV2pFd3lydkQ3eUcyc0pod3RMOU9mUnhjcEh6RzkxUnBqK3pRdGJBT0U5WkQ5L21ScnhVMTBGUmNEN095RUtuSFFUUDg4ZG5MYW9wWEppMU1iL2pkcTgvRE16S0RIazI1Sy9qbEJqS0QwNmlRcExxKzZlb0dUUjNYV2YzVjhBOXkxb2FIUnFrb3R2VThoSGY2SlNnR2cyaFYwU2UrSFptdG5ia2d4dHRpY01mQUg4TnNFdkVMVWpPc2lXYitxZkZaVlAwR2RxZVBuZmM0RmdENnZyVXkxaDFWSjhCRzQzL2lCNUZNVkQ0bk4ycWp3YkFYUUMwNGJ6VUhsYzBpQkQxZEd5SWcyVzQ0Z1hRTU9JMTNXU0FBRSs3ZVB1SEgzeGNtR29EbnZlRnVsV0pFK1RWYUxINnZvNFJQYW1UTithT3RUclZseWh3djwvWDUwOUNlcnRpZmljYXRlPjwvWDUwOURhdGE+PC9LZXlJbmZvPjwvU2lnbmF0dXJlPjwvRFRFPg==");
            $ted = $this->fragmentar_xml($decode_dte, 'TED');// obtener dte para timbre y firma electronica XML DTE
            /*folio*/$doc = $this->fragmentar_xml_folio($decode_dte, 'IdDoc');
            $track_id = '123321';

            return [
                'estado' => 'success',
                'dte' => $decode_dte,
                'ted' => $ted,
                'dte_folio' => (string)$doc->Folio,
                'folio' => random_int(100, 999), //test solo es un test de folio
                'track_id' => $track_id,
                'envio' => $decode_envio,
            ];


        }catch(QueryException $e){
			return [
                'estado' => 'failed',
                'qex' => $e
            ];
        }

		catch(\Exception $e){
            return [
                'estado' => 'failed',
                'qex' => $e
            ];
		}
    }

    public function fragmentar_xml($xml, $tag){

        $arrXml = array();
        $dom = new DOMDocument;
        $dom->loadXML($xml);
        foreach( $dom->getElementsByTagName( ''.$tag.'' ) as $item ) {
            $arrXml[] = $dom->saveXML( $item );
        }
        return( $arrXml[0] );

    }
    public function fragmentar_xml_folio($xml, $tag){

        $arrXml = array();
        $dom = new DOMDocument;
        $dom->loadXML($xml);
        foreach( $dom->getElementsByTagName( ''.$tag.'' ) as $item ) {
            $arrXml[] = $dom->saveXML( $item );


        }
        // return( $arrXml[0] );
        $mixml = new SimpleXMLElement($arrXml[0]);
        $json_string = json_encode($xml);
        $result_array = json_decode($json_string, TRUE);
        return $mixml;

    }

    public function venta_por_referencia_nc(Request $r){
        //  return ($r->all());
        $doc = DB::select("SELECT
        v.id,
        tipo_venta_id dte,
        folio,
        venta_total,
        to_char(v.created_at,'dd/mm/YYYY HH24:MI:SS') emision_cl,
        v.created_at emision_us
        from documento_xml doc
        inner join ventas v on v.id = doc.venta_id
        where doc.folio ='$r->folio'
            and to_char(v.created_at,'YYYY-mm-dd') = '$r->emision'
            and tipo_venta_id = $r->documento");

        if(count($doc) > 0){
            return[
                'estado' => 'success',
                'tabla' => $doc // ventas ref folio
            ];

        }else{
            return[
                'estado'=>'failed',
                'tabla'=>[]
            ];
        }
    }
}
