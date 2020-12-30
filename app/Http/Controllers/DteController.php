<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Configuraciones;
use App\DetalleVenta;
use App\Producto;
use App\RegistroCajaVendedor;
use App\Ventas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DteController extends Controller
{


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
            $new['Productos'][$i]['DescuentoNeto'] = $c['descuento'];
            $new['Productos'][$i]['TipoImpAdicional'] =$c['tipo_impuesto_adicional'];
            $new['Productos'][$i]['MontoImpAdicional'] = $c['monto_impuesto_adicional'];
            $new['Productos'][$i]['UnidadMedida'] = $c['unidad'];
            $new['Productos'][$i]['SubTotal'] =$c['precio'];

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

        if ($venta->save()) {
            $ingresarDetalle = $this->registro_detalle_venta($datos->factura['Productos'], $venta->id);

            $cliente=Cliente::find($datos->cliente_id);

            // dd($ingresarDetalle);
            if ($ingresarDetalle['estado'] == true) {

                // DB::commit();
                // $factura = $datos['factura'];
                // $factura['Total'] = $datos['totales'];

                $factura = [
                    'Encabezado' => [
                        'IdDoc' => [
                            'TipoDTE' => 34,
                            'Folio' => 11,
                        ],
                        'Emisor' => [
                            'RUTEmisor' => '77106553-8',
                            'RznSoc' => 'Neofox Lmtda',
                            'GiroEmis' => 'Servicios integrales de informática',
                            'Acteco' => 726000,
                            'DirOrigen' => 'Los Angeles',
                            'CmnaOrigen' => 'Los Angeles',
                        ],
                        'Receptor' => [
                            'RUTRecep' => '18805652-0',
                            'RznSocRecep' => 'Alejandro Godoy',
                            'GiroRecep' => 'Gobierno',
                            'DirRecep' => 'Quito 1120',
                            'CmnaRecep' => 'Los Angeles',
                        ],
                    ],
                    'Detalle' => [
                        [
                            'NmbItem' => 'Cajón AFECTO',
                            'QtyItem' => 123,
                            'PrcItem' => 923,
                        ],
                        [
                            'NmbItem' => 'Relleno AFECTO',
                            'QtyItem' => 53,
                            'PrcItem' => 1473,
                        ],
                    ],
                ];

                $caratula = [
                    //'RutEnvia' => '11222333-4', // se obtiene de la firma
                    'RutReceptor' => '18805652-0',
                    'FchResol' => '2014-12-05',
                    'NroResol' => 0,
                ];

            ////DATOS PARA LLEGAR A LA FACTURA --------------------------------
                $config = [
                        'firma' => [
                            'file' => '/Users/alejandroesteban/Downloads/firma_digital_certificado.pfx',
                            //'data' => '', // contenido del archivo certificado.p12
                            'pass' => '1028',
                        ],
                    ];
                \sasco\LibreDTE\Sii::setAmbiente(\sasco\LibreDTE\Sii::CERTIFICACION);
                $token = \sasco\LibreDTE\Sii\Autenticacion::getToken($config['firma']);


                // Objetos de Firma y Folios
                $Firma = new \sasco\LibreDTE\FirmaElectronica($config['firma']);
                // dd(file_get_contents(__DIR__.'/folio_prueba_neofox.xml'));
                $Folios = new \sasco\LibreDTE\Sii\Folios(file_get_contents(__DIR__.'/folio_prueba_neofox.xml'));

                // generar XML del DTE timbrado y firmado
                $DTE = new \sasco\LibreDTE\Sii\Dte($factura);
                $DTE->timbrar($Folios);

                $DTE->firmar($Firma);

                //  dd($DTE);


                // generar sobre con el envío del DTE y enviar al SII
                $EnvioDTE = new \sasco\LibreDTE\Sii\EnvioDte();
                $EnvioDTE->agregar($DTE);
                $EnvioDTE->setFirma($Firma);

                $EnvioDTE->setCaratula($caratula);
                $EnvioDTE->generar();

                if ($EnvioDTE->schemaValidate()) {
                    // var_dump( $EnvioDTE->generar());
                    $track_id = $EnvioDTE->enviar();
                    dd($track_id);
                }

                // si hubo algún error se muestra
                foreach (\sasco\LibreDTE\Log::readAll() as $log)
                dd($log,"\n");
            //// FIN DATOS PARA LLEGAR A LA FACTURA ----------------------------------------
                return [
                    'venta' =>$venta,
                    'detalle_ventas' => $ingresarDetalle['detalle_venta'],
                    'factura' => $factura
                ];

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

        return ['fecha' => date('d-m-yy'), 'hora' => date('G:i'), 'date'=> date('yy-m-d')];
    }
}
