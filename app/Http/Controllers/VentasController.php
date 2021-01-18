<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Configuraciones;
use App\Ventas;
use App\Producto;
use Carbon\Carbon;
use App\DetalleVenta;
use App\Pagocredito;
use App\PeriodoCaja;
use App\RegistroCajaVendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\AssignOp\Concat;
use App\User;


use BigFish\PDF417\PDF417;
use BigFish\PDF417\Renderers\ImageRenderer;
use BigFish\PDF417\Renderers\SvgRenderer;
use DOMDocument;

class VentasController extends Controller
{
    protected function registro_venta(Request $datos)
    {
        // dd($datos->all());
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
            $venta->venta_total = $datos->venta_total;
            $venta->pago_efectivo = !empty($datos->pago_efectivo) ?  $datos->pago_efectivo : '0';
            $venta->pago_debito = !empty($datos->pago_debito) ? $datos->pago_debito : '0';

        }

        //validar si fuera forma de pago CONTADO
        if($datos->sii_forma_pago == 'CONTADO'){

            if ($datos->forma_pago_id == '1,undefined') {
                $venta->forma_pago_id = '1';
                if($datos->pago_efectivo == 0 || trim($datos->pago_efectivo)==''){
                    return ['estado'=>'failed', 'mensaje'=>'ingrese el monto en efectivo.'];
                }
                $vuelto = (int)$datos->pago_efectivo - (int)$datos->venta_total;

                if($vuelto < 0){
                    return ['estado' => 'failed', 'mensaje' => 'La deuda del cliente es '.abs($vuelto).', '];
                }
                $venta->vuelto = $vuelto;
            } elseif ($datos->forma_pago_id == '2,undefined') {
                $venta->forma_pago_id = '2';
                if($datos->pago_debito == 0 || trim($datos->pago_debito)==''){
                    return ['estado'=>'failed', 'mensaje'=>'ingrese el monto en efectivo.'];
                }
                $vuelto = (int)$datos->pago_debito - (int)$datos->venta_total;
                if($vuelto < 0){
                    return ['estado' => 'failed', 'mensaje' => 'La deuda del cliente es '.abs($vuelto).', '];
                }
                $venta->vuelto = $vuelto;
            }
            elseif ($datos->forma_pago_id == '1,2'){
                $venta->forma_pago_id = '1,2';
                if($datos->pago_debito == 0 || trim($datos->pago_debito)=='' || $datos->pago_efectivo == 0 || trim($datos->pago_efectivo)=='' ){
                    return ['estado'=>'failed', 'mensaje'=>'ingrese el monto en efectivo y/o debito.'];
                }
                $vuelto = ((int)$datos->pago_efectivo + (int)$datos->pago_debito) - (int)$datos->venta_total;
                if($vuelto < 0){
                    return ['estado' => 'failed', 'mensaje' => 'La deuda del cliente es '.abs($vuelto).', '];
                }
                $venta->vuelto = $vuelto;
            }
            elseif ($datos->forma_pago_id == '2,1'){
                $venta->forma_pago_id = '2,1';
                if($datos->pago_debito == 0 || trim($datos->pago_debito)=='' || $datos->pago_efectivo == 0 || trim($datos->pago_efectivo)=='' ){
                    return ['estado'=>'failed', 'mensaje'=>'ingrese el monto en efectivo y/o debito.'];
                }
                $vuelto = ((int)$datos->pago_efectivo + (int)$datos->pago_debito) - (int)$datos->venta_total;
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
            if($datos->pago_efectivo == null || $datos->pago_debito == null){
                return ['estado'=>'failed', 'mensaje'=>'Abono efectivo o Abono debito sin valores, al menos ingrese un 0'];
            }

            $deuda = $datos->venta_total - ($datos->pago_efectivo + $datos->pago_debito);
            //pago_credito no es el pago como tal, es la deuda ;)
            if($deuda < 0){
                return ['estado'=>'failed', 'mensaje'=>'El monto de la deuda no puede estar en negativo'];
            }
            $venta->pago_credito = $deuda;
            $venta->detalle_credito = $datos->detalle_credito;
            $venta->forma_pago_id = '3';

        }

        if ($datos->tipo_entrega_id == []) {
            return ['estado'=>'failed', 'mensaje'=>'seleccione un tipo de entrega.'];
        } else {
            $venta->tipo_entrega_id = $datos->tipo_entrega_id;
        }
        if ($datos->cliente_id == []) {
            return ['estado'=>'failed', 'mensaje'=>'seleccione un cliente.'];
        } else {
            $venta->cliente_id = $datos->cliente_id;
        }

        //datos totales de boleta dte39

        $venta->totales_iva = $datos->iva;
        $venta->totales_neto = $datos->neto;
        // dd($deuda);

        if ($venta->save()) {
            $ingresarDetalle = $this->registro_detalle_venta($datos->carro, $venta->id);

            $cliente=Cliente::find($datos->cliente_id);

            if ($ingresarDetalle == true) {
                DB::commit(); /*descomentar aqui despues*/
                $ticketDetalle = $this->ticketDetalle($venta->id);
                $ticket = $this->ticket($venta->id);
                if ($ticketDetalle['estado'] == 'success' && $ticket['estado'] == 'success') {
                    $datos_finales = ['estado'=>'success',
                            'mensaje'=>'Venta realizada con exito, actualizando nuevo stock.',
                            'ticketDetalle'=>$ticketDetalle['ticketDetalle'],
                            'ticket'=>$ticket['ticket'],
                            'cliente'=>$cliente->nombres.' '.$cliente->apellidos.' - '.$cliente->rut,
                            'vuelto'=>$vuelto
                            ];
                    // DB::rollBack();
                    return $datos_finales;
                    //return $this->ambiente($datos_finales);
                }
            } else {
                if ($ingresarDetalle == false) {
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

    protected function registro_detalle_venta($carro, $venta_id)
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
                if ($carro[$i]['cantidad_ls'] > $productoCantidad->cantidad) {
                    return false;
                }
            }
        }
        $count = 0;

        DB::beginTransaction();
        for ($i = 0; $i < count($carro); $i++) {
            $venta = new DetalleVenta;
            $venta->user_id = Auth::user()->id;
            $venta->venta_id = $venta_id;
            $venta->producto_id = $carro[$i]['id'];
            $venta->cantidad = $carro[$i]['cantidad_ls'];
            $venta->precio = $carro[$i]['precio'];

            if ($venta->save()) {
                $actualizarCantidad = Producto::find($carro[$i]['id']);

                if ($actualizarCantidad->stock == 'S') {
                    $actualizarCantidad->cantidad = $actualizarCantidad->cantidad - $carro[$i]['cantidad_ls'];
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
            DB::commit();
            return true;
        } else {
            DB::rollBack();
            return false;
        }
    }

    protected function traer_ventas()
    {
        $listar = Ventas::select([
                                            'ventas.id as idVenta',
                                            'ventas.created_at as creado',
                                            'ventas.venta_total',
                                            'users.name as nombreUsuarioVenta',
                                        ])
                                            ->join('users', 'users.id', 'ventas.user_id')
                                            ->orderby('ventas.id', 'desc')
                                            ->get();
        if (count($listar) > 0) {
            foreach ($listar as $key) {
                setlocale(LC_TIME, 'es_CL.UTF-8');
                $key->creado = Carbon::parse($key->creado)->formatLocalized('%d de %B del %Y %H:%M:%S');
            }
        }
        if (!$listar->isEmpty()) {
            return ['estado'=>'success' , 'ventas' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'No existen ventas.'];
        }
    }

    protected function buscar_venta_producto($producto)
    {
        $listar = Ventas::select([
                                    'ventas.id as idVenta',
                                    'ventas.created_at as creado',
                                    'ventas.venta_total',
                                    'users.name as nombreUsuarioVenta',
                                ])
                                    ->join('users', 'users.id', 'ventas.user_id')
                                    ->where('ventas.id', 'LIKE', '%' . $producto . '%')
                                    ->orWhere('ventas.created_at', 'LIKE', '%' . $producto . '%')
                                    ->orWhere('users.name', 'LIKE', '%' . $producto . '%')
                                        ->orderby('ventas.id', 'desc')
                                    ->get();

        if (count($listar) > 0) {
            foreach ($listar as $key) {
                setlocale(LC_TIME, 'es_CL.UTF-8');
                $key->creado = Carbon::parse($key->creado)->formatLocalized('%d de %B del %Y %H:%M:%S');
            }
        }

        if (!$listar->isEmpty()) {
            return ['estado'=>'success' , 'producto' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'El producto no existe en nuestros registros.'];
        }
    }

    protected function total_ventas()
    {
        $listar = DB::table('ventas')->sum('venta_total');
        if ($listar > 0) {
            return $listar;
        } else {
            return 0;
        }
    }

    protected function ultimas_ventas()
    {
        $listar = Ventas::select([
                                    'ventas.id as idVenta',
                                    'ventas.created_at as creado',
                                    'ventas.venta_total',
                                    'users.name as nombreUsuarioVenta',
                                ])
                                    ->join('users', 'users.id', 'ventas.user_id')
                                    ->orderby('ventas.id', 'desc')
                                    ->take(5)
                                    ->get();
        if (count($listar) > 0) {
            foreach ($listar as $key) {
                setlocale(LC_TIME, 'es_CL.UTF-8');
                $key->creado = Carbon::parse($key->creado)->formatLocalized('%d de %B del %Y %H:%M:%S');
            }
        }
        if (!$listar->isEmpty()) {
            return ['estado'=>'success' , 'ventas' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'No existen ventas.'];
        }
    }

    protected function mas_vendidos()
    {
        $listar = DB::select(
            "SELECT * from
            (select
             producto_id,
             producto.nombre
             from detalle_venta
             inner join ventas on ventas.id = detalle_venta.venta_id
             inner join  producto on producto.id = detalle_venta.producto_id
             group by producto_id, producto.nombre) producto
             inner join
             (select producto_id,
             sum(cantidad) as cantidad_total,
             sum(precio) as venta_total ,
			 sum(cantidad * precio) precio
             from detalle_venta
             inner join ventas on ventas.id = detalle_venta.venta_id
			 group by producto_id) venta

             on producto.producto_id = venta.producto_id
             order by venta.venta_total desc
             limit 5"
        );

        if (count($listar) > 0) {
            return ['estado'=>'success' , 'vendidos' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'No existen ventas.'];
        }
    }

    protected function buscar_producto_carro($producto)
    {
        $precio = empty(Auth::user()->tipo_precio)? 1 : Auth::user()->tipo_precio;

        if($precio == 1){
            $tipo_precio = 'precio_1';
        }
        if($precio == 2){
            $tipo_precio = 'precio_2';
        }

        // $listar = Producto::select([
        //                             'producto.id',
        //                             'producto.sku',
        //                             'producto.nombre',
        //                             'producto.cantidad',
        //                             'producto.'.$tipo_precio.' as precio',
        //                             ])
        //                             ->where('producto.activo','S')
        //                             ->whereRaw(
        //                                 "lower(producto.nombre) like lower('%$producto%') or
        //                                  lower(producto.sku) like lower('%$producto%')"
        //                             )


        //                             ->get();

        $listar = DB::select("SELECT producto.id,
                producto.sku,
                producto.nombre,
                producto.cantidad,
                producto.$tipo_precio as precio
        from producto where producto.activo = 'S' and (
        lower(producto.nombre) like lower('$producto') or
        lower(producto.sku) like lower('$producto') and producto.deleted_at is null)");

        if (count($listar) > 0) {
            foreach ($listar as $key) {
                $key->cantidad_ls = 1;
                $key->unidad = 'c/u'; // unidad por defecto, el user la puede cambiar
                $key->afecto = "true"; //true: si, false:no , true por defecto
                $key->tipo_impuesto_adicional = 0;//ninguno por defecto al ingresar
                $key->monto_impuesto_adicional = 0;//ninguno por defecto al ingresar
                $key->descuento = 0; // %
                $key->monto_descuento = 0;
                $key->item_neto = 0;
                $key->item_descontado = 0;

            }
            return ['estado'=>'success' , 'producto' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'El producto no existe o esta inhabilitado'];
        }
    }

    protected function traer_detalle_venta($idVenta)
    {
        $listar = DetalleVenta::select([
                                        'detalle_venta.cantidad as cantidadDetalle',
                                        'detalle_venta.precio',
                                        'producto.nombre',
                                        'producto.descripcion as proDesc',
                                        'categoria.descripcion as catDesc',
                                        'categoria.id as catId',
                                        'producto.imagen',
                                        'cliente.nombres',
                                        'cliente.apellidos',
                                        'detalle_venta.descuento',
                                        'detalle_venta.impuesto_adicional',
                                        'detalle_venta.tipo_impuesto_adicional',
                                        'detalle_venta.porcentaje_descuento',
                                        'detalle_venta.afecto_iva',
                                        'detalle_venta.unidad'
                                        ])
                                        ->join('ventas', 'ventas.id', 'detalle_venta.venta_id')
                                        ->join('producto', 'producto.id', 'detalle_venta.producto_id')
                                        ->leftJoin('categoria', 'categoria.id', 'producto.categoria_id')
                                        ->join('users', 'users.id', 'ventas.user_id')
                                        ->join('cliente','cliente.id','ventas.cliente_id')
                                        ->orderby('ventas.id', 'desc')
                                        ->where('venta_id', $idVenta)
                                        ->get();



        if (!$listar->isEmpty()) {
            return ['estado'=>'success' , 'detalleVenta' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'No existen ventas.'];
        }
    }

    protected function mas_vendidos_grafico()
    {
        $listar = DB::select(
            " SELECT * from
            (select
             producto_id,
             producto.nombre
             from detalle_venta
             inner join ventas on ventas.id = detalle_venta.venta_id
             inner join  producto on producto.id = detalle_venta.producto_id
             group by producto_id, producto.nombre) producto
             inner join
             (select producto_id,
             sum(cantidad) as cantidad_total,
             sum(venta_total) as venta_total
             from detalle_venta
             inner join ventas on ventas.id = detalle_venta.venta_id
             group by producto_id) venta

             on producto.producto_id = venta.producto_id
             order by venta.venta_total desc
             limit 5"
        );

        $json_producto = [];
        $json_cantidad = [];
        $json_total = [];

        foreach ($listar as $key) {
            $json_producto[] = $key->nombre;
            $json_cantidad[] = $key->cantidad_total;
            $json_total[] = $key->venta_total;
        }

        return [
            'labels' => $json_producto,
            'datasets' =>[
                [
                    'label' => 'CANTIDAD',
                    'data' => $json_cantidad,
                    'backgroundColor' => [
                        '#D35400',
                        '#F1C40F',
                        '#28B463',
                        '#2980B9',
                        '#7D3C98'
                    ],
                    'hoverBackgroundColor' => [
                        '#E59866',
                        '#F9E79F',
                        '#A9DFBF',
                        '#7FB3D5',
                        '#C39BD3'
                    ],
                ],
            ]
        ];
    }

    protected function ultimas_ventas_grafico()
    {
        $listar = Ventas::select([
                                    'ventas.id as idVenta',
                                    'ventas.created_at as creado',
                                    'ventas.venta_total',
                                    'users.name as nombreUsuarioVenta',
                                ])
                                    ->join('users', 'users.id', 'ventas.user_id')
                                    ->orderby('ventas.id', 'desc')
                                    ->take(5)
                                    ->get();

        $json_idVenta = [];
        $json_total = [];

        foreach ($listar as $key) {
            $json_idVenta[] = $key->idVenta;
            $json_total[] = $key->venta_total;
        }

        return [
            'labels' => $json_idVenta,
            'datasets' =>[
                [
                    'label' => 'VENTAS',
                    'data' => $json_total,
                    'backgroundColor' => [
                        '#D35400',
                        '#F1C40F',
                        '#28B463',
                        '#2980B9',
                        '#7D3C98'
                    ],
                    'hoverBackgroundColor' => [
                        '#E59866',
                        '#F9E79F',
                        '#A9DFBF',
                        '#7FB3D5',
                        '#C39BD3'
                    ],
                ],
            ]
        ];
    }

    protected function menos_vendidos_grafico()
    {
        $listar = DB::select(
            " SELECT * from
            (select
             producto_id,
             producto.nombre
             from detalle_venta
             inner join ventas on ventas.id = detalle_venta.venta_id
             inner join  producto on producto.id = detalle_venta.producto_id
             group by producto_id, producto.nombre) producto
             inner join
             (select producto_id,
             sum(cantidad) as cantidad_total,
             sum(venta_total) as venta_total
             from detalle_venta
             inner join ventas on ventas.id = detalle_venta.venta_id
             group by producto_id) venta

             on producto.producto_id = venta.producto_id
             order by venta.venta_total asc
             limit 5"
        );

        $json_producto = [];
        $json_cantidad = [];
        $json_total = [];

        foreach ($listar as $key) {
            $json_producto[] = $key->nombre;
            $json_cantidad[] = $key->cantidad_total;
            $json_total[] = $key->venta_total;
        }

        return [
            'labels' => $json_producto,
            'datasets' =>[
                [
                    'label' => 'CANTIDAD',
                    'data' => $json_cantidad,
                    'backgroundColor' => [
                        '#D35400',
                        '#F1C40F',
                        '#28B463',
                        '#2980B9',
                        '#7D3C98'
                    ],
                    'hoverBackgroundColor' => [
                        '#E59866',
                        '#F9E79F',
                        '#A9DFBF',
                        '#7FB3D5',
                        '#C39BD3'
                    ],
                ],
            ]
        ];
    }

    protected function reporte_ventas($desde = '', $hasta = '')
    {

        if (isset($desde) && isset($hasta)) {
            $listar = Ventas::select([
                'ventas.id as idVenta',
                'ventas.created_at as creado',
                'ventas.venta_total',
                'users.name as nombreUsuarioVenta',
                'ventas.pago_efectivo',
                'ventas.pago_debito',
                'ventas.vuelto',
                'ventas.pago_credito',
                'ventas.totales_impuesto_especifico',
                'ventas.totales_iva',
                'ventas.totales_neto',
                'ventas.tipo_venta_id as dte',
                DB::raw("
                    case
                        when cliente.tipo_cliente = 'PERSONA' then concat(cliente.nombres,' ',cliente.apellidos)
                        when cliente.tipo_cliente = 'EMPRESA' then cliente.razon_social
                    end as cliente
                ")
            ])
                ->join('users', 'users.id', 'ventas.user_id')
                ->join('cliente', 'cliente.id', 'ventas.cliente_id')
                // ->leftJoin('credito_deuda', 'credito_deuda.venta_id','ventas.id')
                ->whereBetween('ventas.created_at', [$desde, $hasta])
                // ->where('ventas.activo','S')
                ->orderby('ventas.id', 'desc')
                ->get();



            $suma_ventas = 0;
            $suma_credito = 0;
            $suma_vuelto = 0;
            $efectivo_real = 0; //efectivo - vuelto
            $credito = 0;
            $debito = 0; //aqui no vamos a permitir vueltos cuando se pague unicamente con debito
            if (count($listar) > 0) {
                foreach ($listar as $key) {
                    setlocale(LC_TIME, 'es_CL.UTF-8');
                    $key->creado = Carbon::parse($key->creado)->formatLocalized('%d de %B del %Y %H:%M:%S');
                    $suma_ventas += $key->venta_total;
                    $suma_credito += $key->monto_credito;
                    $suma_vuelto += $key->vuelto;
                    $efectivo_real += ($key->pago_efectivo - $key->vuelto);
                    $debito += $key->pago_debito;
                    $credito += $key->pago_credito;
                }
            }
            if (!$listar->isEmpty()) {
                $dd = date('d/m/Y H:i', strtotime($desde));
                $hh = date('d/m/Y H:i', strtotime($hasta));
                return [
                        'estado'=>'success' ,
                        'ventas' => $listar,
                        'total'=>$suma_ventas,
                        'deuda'=>$suma_credito,
                        'vuelto'=>$suma_vuelto,
                        'efectivo_real' => $efectivo_real,
                        'debito' => $debito,
                        'credito' => $credito,
                        'fecha'=> 'Resumen desde '.$dd.' hrs -  hasta '.$hh.' hrs'

                    ];
            } else {
                return ['estado'=>'failed', 'mensaje'=>'No existen ventas en el rango de fecha seleccionado.'];
            }
        }
    }

    protected function ticketDetalle($idVenta)
    {
        $listar = DetalleVenta::select([
            'detalle_venta.cantidad as cantidadDetalle',
            'detalle_venta.precio',
            'producto.nombre',
            ])
            ->join('ventas', 'ventas.id', 'detalle_venta.venta_id')
            ->join('producto', 'producto.id', 'detalle_venta.producto_id')
            ->join('categoria', 'categoria.id', 'producto.categoria_id')
            ->join('users', 'users.id', 'ventas.user_id')
            ->where('venta_id', $idVenta)
            ->get();

        if (!$listar->isEmpty()) {
            return ['estado'=>'success' , 'ticketDetalle' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'No existen ventas.'];
        }
    }
    protected function ticket($idVenta)
    {
        $listar = Ventas::select([
            'ventas.id as idVenta',
            'ventas.created_at as fechaVenta',
            'ventas.venta_total as totalVenta',
            'ventas.totales_iva'
            ])
            ->where('ventas.id', $idVenta)
            ->get();

        if (!$listar->isEmpty()) {
            return ['estado'=>'success' , 'ticket' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'No existen ventas.'];
        }
    }



    //@ este metodo trae el comprobante de venta, ya sea vaucher, factura o boleta
    public function comprobante($venta_id){

        $venta = Ventas::find($venta_id);

        //si la venta es un vaucher---------------
        if($venta->tipo_venta_id == 1){
            $conf = Configuraciones::all();

            $venta_db = DB::select("SELECT ventas.id,
                                    venta_total,
                                    to_char(ventas.created_at, 'dd/mm/yyy hh24:MI') fecha,
                                    vuelto,
                                    concat(c.nombres,' ',c.apellidos) cliente,
                                    tipo_venta_id
                                    from ventas
                                    inner join cliente c on c.id = ventas.cliente_id
                                    where ventas.id = $venta_id");

            if(count($venta_db) > 0){
                $venta_detalle = DB::select("SELECT
                                            dv.id,
                                            p.id producto_id,
                                            p.nombre,
                                            dv.cantidad,
                                            dv.precio
                                        from detalle_venta dv
                                        inner join producto p on p.id = dv.producto_id
                                        where venta_id = $venta_id");

                    return [
                        'estado' => 'success',
                        'configuraciones' => $conf[0],
                        'venta' => $venta_db[0],
                        'venta_detalle' => $venta_detalle
                    ];
            }
            return ['estado'=>'failed'];


        }
        //si la venta es un vaucher---------------

         //si la venta es una factura---------------
         if($venta->tipo_venta_id == 33){
            // $conf = Configuraciones::all();

            $venta_db = DB::select("SELECT ventas.id,
                                    venta_total,
                                    to_char(ventas.created_at, 'dd/mm/yyy hh24:MI') fecha,
                                    vuelto,
                                    concat(c.nombres,' ',c.apellidos) cliente,
                                    tipo_venta_id,
                                    tipo_venta_id as dte,
                                    totales_impuesto_especifico,
                                    totales_iva,
                                    totales_neto,
                                    totales_exento,
                                    forma_pago_id,
                                    cliente_id,
                                    regexp_replace(dx.ted, '\s', '', 'g') ted,
                                    dx.folio
                                    from ventas
                                    inner join cliente c on c.id = ventas.cliente_id
                                    left join documento_xml dx on dx.venta_id = ventas.id
                                    where ventas.id = $venta_id");







            if(count($venta_db) > 0){
                $venta_detalle = DB::select("SELECT
                                            dv.id,
                                            p.id producto_id,
                                            upper(p.nombre) nombre,
                                            upper(p.descripcion) descripcion,
                                            dv.cantidad,
                                            dv.precio,
                                            dv.descuento,
                                            tipo_impuesto_adicional,
                                            impuesto_adicional,
                                            unidad

                                        from detalle_venta dv
                                        inner join producto p on p.id = dv.producto_id
                                        where venta_id = $venta_id");



                $new = [];
                $reseptor = Cliente::find($venta_db[0]->cliente_id);
                $emisor = Configuraciones::all();

                $emisor['emisor'] = $emisor[0];

                $new['Llave'] = 'test key';
                $new['Fecha'] = $venta_db[0]->fecha;

                if($venta_db[0]->forma_pago_id =='3'){
                    $sii_forma_pago='CREDITO';
                }else{
                    $sii_forma_pago='CONTADO';
                }

                if($sii_forma_pago == 'CONTADO'){
                    $new['FormaPago'] = 0;

                }
                if($sii_forma_pago == 'CREDITO'){
                    $new['FormaPago'] = 1;

                }
                $new['FormaPago_str'] = $sii_forma_pago;

                $new['emisor'] = $emisor['emisor'];
                $new['emisor']['empresa'] = strtoupper($emisor[0]['empresa']);
                $new['emisor']['giro'] = strtoupper($emisor[0]['giro']);
                $new['emisor']['rut'] = strtoupper($emisor[0]['rut']);
                $new['emisor']['dirreccion'] = strtoupper($emisor[0]['dirreccion']);

                $new['Cliente']['Ciudad'] = strtoupper($reseptor['ciudad']);
                $new['Cliente']['RazonSocial'] = ($reseptor['tipo_cliente'] =='PERSONA')? strtoupper($reseptor['nombres'].' '.$reseptor['apellidos']): $reseptor['razon_social'];
                $new['Cliente']['Comuna'] = strtoupper($reseptor['comuna']);
                $new['Cliente']['Contacto'] = strtoupper($reseptor['contacto']);
                $new['Cliente']['Direccion'] = strtoupper($reseptor['direccion']);
                $new['Cliente']['Email'] = strtoupper($reseptor['email']);
                $new['Cliente']['Giro'] = strtoupper($reseptor['giro']);
                $new['Cliente']['Rut'] = strtoupper($reseptor['rut']);

                $i=0;
                foreach ($venta_detalle as $c) {
                    $new['Productos'][$i]['id'] = $c->id;
                    $new['Productos'][$i]['NombreProducto'] = strtoupper($c->nombre);
                    $new['Productos'][$i]['DescripcionAdicional'] = strtoupper('...');
                    $new['Productos'][$i]['Afecto'] = true;
                    $new['Productos'][$i]['Cantidad'] = $c->cantidad;
                    $new['Productos'][$i]['PrecioNeto'] = $c->precio;
                    $new['Productos'][$i]['DescuentoNeto'] = $c->descuento;
                    $new['Productos'][$i]['TipoImpAdicional'] =$c->tipo_impuesto_adicional;
                    $new['Productos'][$i]['MontoImpAdicional'] = $c->impuesto_adicional;
                    $new['Productos'][$i]['UnidadMedida'] = $c->unidad;
                    $new['Productos'][$i]['SubTotal'] =$c->precio;

                    $i++;
                }

                    return [
                        'estado' => 'success',
                        'configuraciones' => $emisor,
                        'venta' => $venta_db[0],
                        'venta_detalle' => $venta_detalle,
                        'factura' => $new
                    ];
            }
            return ['estado'=>'failed'];


        }
        //si la venta es un vaucher---------------

    }

    protected function periodico_ventas_grafico($anio){
        $meses = [1,2,3,4,5,6,7,8,9,10,11,12];
        $obj = [];
        $val = [];

        foreach ($meses as $key) {
            $moment = DB::select("SELECT sum(venta_total) venta_total from ventas where
            EXTRACT(MONTH FROM created_at) = $key
            and EXTRACT(year FROM created_at) = $anio");

            $obj[$key] = $moment[0];

        }
        foreach ($obj as $key) {
            $val[] = !empty($key->venta_total)?$key->venta_total:0;
        }

        return [
            'labels' => ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            'datasets' =>[
                [
                    'label' => 'Venta',
                    'data' => $val,
                    'backgroundColor' => [
                        '#D35400',
                        '#F1C40F',
                        '#28B463',
                        '#2980B9',
                        '#7D3C98',
                        '#D35400',
                        '#F1C40F',
                        '#28B463',
                        '#2980B9',
                        '#7D3C98',
                        '#D35400',
                        '#F1C40F',
                        '#28B463',
                        '#2980B9',
                        '#7D3C98'
                    ],
                    'hoverBackgroundColor' => [
                        '#E59866',
                        '#F9E79F',
                        '#A9DFBF',
                        '#7FB3D5',
                        '#C39BD3',
                        '#D35400',
                        '#F1C40F',
                        '#28B463',
                        '#2980B9',
                        '#7D3C98',
                        '#D35400',
                        '#F1C40F',
                        '#28B463',
                        '#2980B9',
                        '#7D3C98'
                    ],
                ],
            ]
        ];

        return response()->json($obj);
    }


public function pagar_credito(Request $r){

    DB::beginTransaction();

    $p = new Pagocredito;
    $p->venta_id = $r->venta_id;
    $p->pago = 'S';
    $p->descripcion = $r->detalle_credito;
    $p->monto_pago = $r->monto_credito;
    if($p->save()){
        $venta = Ventas::find($r->venta_id);

        //si el monto ingresado es mayor a la deuda entonces
        if($r->monto_credito > $venta->pago_credito){
            //ingnorar el pago
            return ['estado'=>'failed','mensaje'=>'el monto ingresado es mayor a la deuda'];
        }
        //si el monto a pagar es menor que la deuda, entonces
        if($r->monto_credito < $venta->pago_credito){
            //restar a la deuda el input del monto a ingresar
            // $venta->pago_credito = (int)$venta->pago_credito - (int)$r->monto_credito;
        }
        //si el monto a ingresar es igual a la deuda entonces dejar el campo nulo
        if($r->monto_credito == $venta->pago_credito){
            // $venta->pago_credito = null;
        }

        if($venta->save()){
            DB::commit();
            return [
                'estado'=>'success',
                'mensaje'=>'Pago con exito'
            ];
        }
        DB::rollBack();
        // return ['venta'=>$venta, 'pago_credito'=>$p];

        DB::rollBack();


    }
}

















































    public function ambiente(Array $datos_venta){
        // activar todos los errores
        // ini_set('display_errors', true);
        // error_reporting(E_ALL);

        // // zona horaria
        // date_default_timezone_set('America/Santiago');


        // $config = [
        //     'firma' => [
        //         'file' => '/Users/alejandroesteban/Downloads/firma_digital_certificado.pfx',
        //         //'data' => '', // contenido del archivo certificado.p12
        //         'pass' => '1028',
        //     ],
        // ];

        // // trabajar en ambiente de certificación
        // \sasco\LibreDTE\Sii::setAmbiente(\sasco\LibreDTE\Sii::CERTIFICACION);

        // // solicitar token
         $token = \sasco\LibreDTE\Sii\Autenticacion::getToken($config['firma']);
        // var_dump($token);

        // si hubo errores se muestran
        foreach (\sasco\LibreDTE\Log::readAll() as $error) {
            echo $error,"\n";
        }

        $obtener_rut_cliente = explode(' - ',$datos_venta['cliente']);


        $rut_cliente = (string) $this->div_rut($datos_venta['cliente']);


        $detalle = [];
        $recorre_detalle = 0;
        foreach ($datos_venta['ticketDetalle'] as $key) {

            $detalle[$recorre_detalle]['NmbItem'] = $key['nombre'];
            $detalle[$recorre_detalle]['QtyItem'] = $key['cantidadDetalle'];
            $detalle[$recorre_detalle]['PrcItem'] = $key['precio'];

            $recorre_detalle++;
        }

        // datos
         $factura = [
             'Encabezado' => [
                'IdDoc' => [
                    'TipoDTE' => 33,
                    'Folio' => 1,
                ],
                'Emisor' => [
                    'RUTEmisor' => '77106553-8',
                    'RznSoc' => 'NEOFOX INFORMATICA LIMITADA',
                    'GiroEmis' => 'Informatico',
                    'Acteco' => 500000,
                    'DirOrigen' => 'RIO YAQUI 1971 ',
                    'CmnaOrigen' => 'LOS ANGELES',
                ],
                'Receptor' => [
                    'RUTRecep' => $rut_cliente,
                    'RznSocRecep' => $obtener_rut_cliente[0],
                    'GiroRecep' => 'tareas de Gobierno',
                    'DirRecep' => 'Villo pto alegre, los angeles',
                    'CmnaRecep' => 'Los Angeles, chile',
                ],
             ],
            'Detalle' => $detalle,
         ];


         return $factura;



    //     $caratula = [
    //         //'RutEnvia' => '11222333-4', // se obtiene de la firma
    //         'RutReceptor' => '60803000-K',
    //         'FchResol' => '2014-12-05',
    //         'NroResol' => 0,
    //     ];

        $xml = file_get_contents(storage_path('xml/folios/33.xml'));
        // Objetos de Firma y Folios
        $Firma = new \sasco\LibreDTE\FirmaElectronica($config['firma']);
        $Folios = new \sasco\LibreDTE\Sii\Folios($xml);
        return response()->json($Folios);

        // generar XML del DTE timbrado y firmado
        $DTE = new \sasco\LibreDTE\Sii\Dte($factura);
        $DTE->timbrar($Folios);
        $DTE->firmar($Firma);


        // generar sobre con el envío del DTE y enviar al SII
        $EnvioDTE = new \sasco\LibreDTE\Sii\EnvioDte();
        $EnvioDTE->agregar($DTE);
        $EnvioDTE->setFirma($Firma);
        $EnvioDTE->setCaratula($caratula);
        $EnvioDTE->generar();
       //Folio del DTE LibreDTE_T33F1 está fuera de rango
        if ($EnvioDTE->schemaValidate()) {
            echo $EnvioDTE->generar();
            //$track_id = $EnvioDTE->enviar();
            //var_dump($track_id);
        }

        // si hubo algún error se muestra
        foreach (\sasco\LibreDTE\Log::readAll() as $log)
            echo $log,"\n";



    }

    public function cambiar_tipo_precio($precio){

        $u = User::find(Auth::user()->id);
        $u->tipo_precio = $precio;

        if($u->save()){
            return [
                'user' => $u,
                'estado' => 'success'
            ];
        }

    }

    public function fragmentar_xml($xml){

        $arrXml = array();
        $dom = new DOMDocument;
        $dom->loadXML($xml);
        foreach( $dom->getElementsByTagName( 'TED' ) as $item ) {
            $arrXml[] = $dom->saveXML( $item );
        }
        return( $arrXml );

    }

    public function generar_un_xml(){

        $arrXml = array();
        $dom    = new DOMDocument;


        $xml = /*new \SimpleXMLElement*/('<DTE version="1.0">
        <Documento ID="MSTIC77033461-633520201228100053">
        <Encabezado>
        <IdDoc>
        <TipoDTE>33</TipoDTE>
        <Folio>5</Folio>
        <FchEmis>2020-12-28</FchEmis>
        <FmaPago>1</FmaPago>
        </IdDoc>
        <Emisor>
        <RUTEmisor>77033461-6</RUTEmisor>
        <RznSoc>SERVICIOS TIC ERIK ALEXIS MILLAR BUSTOS E.I.R.L.</RznSoc>
        <GiroEmis>ACTIVIDADES DE CONSULTORIA DE INFORMATICA Y DE GESTION DE INSTALACIONE</GiroEmis>
        <Acteco>620200</Acteco>
        <Acteco>620900</Acteco>
        <DirOrigen>ALFONSO CARRASCO 288</DirOrigen>
        <CmnaOrigen>LOS ANGELES</CmnaOrigen>
        </Emisor>
        <Receptor>
        <RUTRecep>15629658-9</RUTRecep>
        <RznSocRecep>Erik Millar Bustos</RznSocRecep>
        <GiroRecep>personal</GiroRecep>
        <DirRecep>alfonso carrasco 228</DirRecep>
        <CmnaRecep>los angeles</CmnaRecep>
        <CiudadRecep>Los �ngeles</CiudadRecep>
        </Receptor>
        <Totales>
        <MntNeto>1760</MntNeto>
        <TasaIVA>19</TasaIVA>
        <IVA>334</IVA>
        <ImptoReten>
        <TipoImp>28</TipoImp>
        <TasaImp>0.15</TasaImp>
        <MontoImp>371</MontoImp>
        </ImptoReten>
        <MntTotal>2465</MntTotal>
        </Totales>
        </Encabezado>
        <Detalle>
        <NroLinDet>1</NroLinDet>
        <NmbItem>Producto Prueba 1</NmbItem>
        <QtyItem>2</QtyItem>
        <PrcItem>100</PrcItem>
        <MontoItem>200</MontoItem>
        </Detalle>
        <Detalle>
        <NroLinDet>2</NroLinDet>
        <NmbItem>Producto Prueba 2</NmbItem>
        <DscItem>Producto con Impuestos especifico</DscItem>
        <QtyItem>5</QtyItem>
        <PrcItem>312</PrcItem>
        <CodImpAdic>28</CodImpAdic>
        <MontoItem>1560</MontoItem>
        </Detalle>
        <TED version="1.0">
        <DD>
        <RE>77033461-6</RE>
        <TD>33</TD>
        <F>5</F>
        <FE>2020-12-28</FE>
        <RR>15629658-9</RR>
        <RSR>Erik Millar Bustos</RSR>
        <MNT>2465</MNT>
        <IT1>Producto Prueba 1</IT1>
        <CAF version="1.0">
        <DA>
        <RE>77033461-6</RE>
        <RS>SERVICIOS TIC ERIK ALEXIS MILLAR BUSTOS</RS>
        <TD>33</TD>
        <RNG>
        <D>1</D>
        <H>10</H>
        </RNG>
        <FA>2020-12-27</FA>
        <RSAPK>
        <M>qh431KRpN3aDEHvkBe0NecvZDPUrFW/SVY+xzD0dWT27XhFLNOZmLJjv75l+Ait9I5zL/fAxNIXwqydOGxN+0w==</M>
        <E>Aw==</E>
        </RSAPK>
        <IDK>100</IDK>
        </DA>
        <FRMA algoritmo="SHA1withRSA">OQP4TTl3eRuNpGhY0d0iuijdkJmskvxh0UyTiB1eJbR0Zg4nPHFxsydvIb3rouplN4mlKgiF8B/Kuibw40zrcg==</FRMA>
        </CAF>
        <TSTED>2021-01-14T13:55:49</TSTED>
        </DD>
        <FRMT algoritmo="SHA1withRSA">QIgRgirt0aADFkhN477JiUF85ltnhu3yKRCCoHKbqngXxyD0WvZlHB3bG2MoHP3gKhIaFEiD0S9eniJqhuUFGQ==</FRMT>
        </TED>
        <TmstFirma>2021-01-14T13:55:49</TmstFirma>
        </Documento>
        <Signature xmlns="http://www.w3.org/2000/09/xmldsig#"><SignedInfo><CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /><SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1" /><Reference URI="#MSTIC77033461-633520201228100053"><Transforms><Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /></Transforms><DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1" /><DigestValue>egZVO/gXh4Rc4VdYo8aJvL92MQI=</DigestValue></Reference></SignedInfo><SignatureValue>VkRnl+PkrhbBrzpe8em864U8NizXv3r/v2k4xvcN/E7lRqKLvHzCfEF1vpv1fVIFJZ2NJM2F3p7RBgMMUohW0/9y6huo0xLJXzalSvxmqPNLq2eMZhNFi7rGwYZlkhf8/NNuk+WJqFQYHBrVzH/fTMgj47SIN2NhkZS7s7e7GUk=</SignatureValue><KeyInfo><KeyValue><RSAKeyValue><Modulus>hosjrEAp2ls88ENP3NGEXbZKkWMnrU3udWh04teCILWrnulTIVG1IHIW7/WJ8Sm3wgrnZVoxFXSt8x8aHlIJm+UBXWPMRnQ7+OuxY9sOXOO5vJ01K+MU9n05HzfSn1LYRUgvSQsGrYginr6CCxWfsqq91D1yr6xtXhgwvgyHZJc=</Modulus><Exponent>AQAB</Exponent></RSAKeyValue></KeyValue><X509Data><X509Certificate>MIIGLzCCBRegAwIBAgIKHkLkswAAABN8uTANBgkqhkiG9w0BAQUFADCB0jELMAkGA1UEBhMCQ0wxHTAbBgNVBAgTFFJlZ2lvbiBNZXRyb3BvbGl0YW5hMREwDwYDVQQHEwhTYW50aWFnbzEUMBIGA1UEChMLRS1DRVJUQ0hJTEUxIDAeBgNVBAsTF0F1dG9yaWRhZCBDZXJ0aWZpY2Fkb3JhMTAwLgYDVQQDEydFLUNFUlRDSElMRSBDQSBGSVJNQSBFTEVDVFJPTklDQSBTSU1QTEUxJzAlBgkqhkiG9w0BCQEWGHNjbGllbnRlc0BlLWNlcnRjaGlsZS5jbDAeFw0yMTAxMDkxNjI0NTRaFw0yNDAxMDkxNjI0NTRaMIGwMQswCQYDVQQGEwJDTDEQMA4GA1UECAwHQklPQsONTzEQMA4GA1UEBwwHQklPQsONTzEjMCEGA1UEChMaRklERUwgQVJOT0xETyBJU0xBIEdBUkNJQSAxCjAIBgNVBAsMASoxIjAgBgNVBAMTGUZJREVMIEFSTk9MRE8gSVNMQSBHQVJDSUExKDAmBgkqhkiG9w0BCQEWGUxJTkRBTkEuRlVFTlRFU0BHTUFJTC5DT00wgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAIaLI6xAKdpbPPBDT9zRhF22SpFjJ61N7nVodOLXgiC1q57pUyFRtSByFu/1ifEpt8IK52VaMRV0rfMfGh5SCZvlAV1jzEZ0O/jrsWPbDlzjubydNSvjFPZ9OR830p9S2EVIL0kLBq2IIp6+ggsVn7KqvdQ9cq+sbV4YML4Mh2SXAgMBAAGjggKpMIICpTCCAU8GA1UdIASCAUYwggFCMIIBPgYIKwYBBAHDUgUwggEwMC0GCCsGAQUFBwIBFiFodHRwOi8vd3d3LmUtY2VydGNoaWxlLmNsL0NQUy5odG0wgf4GCCsGAQUFBwICMIHxHoHuAEUAbAAgAHIAZQBzAHAAbwBuAGQAZQByACAAZQBzAHQAZQAgAGYAbwByAG0AdQBsAGEAcgBpAG8AIABlAHMAIAB1AG4AIAByAGUAcQB1AGkAcwBpAHQAbwAgAGkAbgBkAGkAcwBwAGUAbgBzAGEAYgBsAGUAIABwAGEAcgBhACAAZABhAHIAIABpAG4AaQBjAGkAbwAgAGEAbAAgAHAAcgBvAGMAZQBzAG8AIABkAGUAIABjAGUAcgB0AGkAZgBpAGMAYQBjAGkA8wBuAC4AIABQAG8AcwB0AGUAcgBpAG8AcgBtAGUAbgB0AGUALDAdBgNVHQ4EFgQUZt/fe/hTuSvbh+yt951AOAEQVLIwCwYDVR0PBAQDAgTwMCMGA1UdEQQcMBqgGAYIKwYBBAHBAQGgDBYKMDk1NjEzNDAtMzAfBgNVHSMEGDAWgBR44T6f0hKzejyNzTAOU7NDKQezVTA+BgNVHR8ENzA1MDOgMaAvhi1odHRwOi8vY3JsLmUtY2VydGNoaWxlLmNsL2VjZXJ0Y2hpbGVjYUZFUy5jcmwwOgYIKwYBBQUHAQEELjAsMCoGCCsGAQUFBzABhh5odHRwOi8vb2NzcC5lY2VydGNoaWxlLmNsL29jc3AwPQYJKwYBBAGCNxUHBDAwLgYmKwYBBAGCNxUIgtyDL4WTjGaF1Z0XguLcJ4Hv7DxhgcueFIaoglgCAWQCAQQwIwYDVR0SBBwwGqAYBggrBgEEAcEBAqAMFgo5NjkyODE4MC01MA0GCSqGSIb3DQEBBQUAA4IBAQCz2yPxhsp9ZdQAWjEwyrvD7yG2sJhwtL9OfRxcpHzG91Rpj+zQtbAOE9ZD9/mRrxU10FRcD7OyEKnHQTP88dnLaopXJi1Mb/jdq8/DMzKDHk25K/jlBjKD06iQpLq+6eoGTR3XWf3V8A9y1oaHRqkotvU8hHf6JSgGg2hV0Se+HZmtnbkgxtticMfAH8NsEvELUjOsiWb+qfFZVP0GdqePnfc4FgD6vrUy1h1VJ8BG43/iB5FMVD4nN2qjwbAXQC04bzUHlc0iBD1dGyIg2W44gXQMOI13WSAAE+7ePuHH3xcmGoDnveFulWJE+TVaLH6vo4RPamTN+aOtTrVlyhwv</X509Certificate></X509Data></KeyInfo></Signature></DTE>
        ');

        $dom->loadXML( $xml );




        foreach( $dom->getElementsByTagName( 'TED' ) as $item ) {
            $arrXml[] = $dom->saveXML( $item );
        }
        return( $arrXml );
        return false;


        dd($xml);
        $timbre = $xml->SetDTE->DTE->Documento->TED;
        $timbre->DD->RE[0];
        // return response()->json(
        // DD ---------
        $RE = $timbre->DD->RE[0]; //Rut emisor
        $TD = $timbre->DD->TD[0]; // tipo documento
        $F = $timbre->DD->F[0]; // Folio
        $FE = $timbre->DD->FE[0]; // Fecha emision
        $RR = $timbre->DD->RR[0]; // Rut reseptor
        $RSR = $timbre->DD->RSR[0]; // Run Seror
        $MNT = $timbre->DD->MNT[0]; // monto
        $IT1 = $timbre->DD->IT1[0]; // DESCONOCIDO

        // DD->CAF->da
        $C_RE = preg_replace('/\s+/', '',$timbre->DD->CAF->DA->RE[0]); // Rur emisor
        $C_RS = preg_replace('/\s+/', '',$timbre->DD->CAF->DA->RS[0]); // Rason social
        $C_TD = $timbre->DD->CAF->DA->TD[0]; // TIPO DOCUMENTO
        $C_RNG = $timbre->DD->CAF->DA->RNG; //RANFGO DESDE HASTA (D Y H);
        $C_FA = preg_replace('/\s+/', '',$timbre->DD->CAF->DA->FA[0]); // FECHA A
        $C_RSAPK = $timbre->DD->CAF->DA->RSAPK; // E Y M
        $IDK = preg_replace('/\s+/', '',$timbre->DD->CAF->DA->IDK[0]); // DESCONOCIDO
        $FRMA = preg_replace('/\s+/', '',$timbre->DD->CAF->FRMA[0]); // FIRMA

        $TSTED = $timbre->DD->TSTED[0]; // desconocido
        $FRMT = preg_replace('/\s+/', '',$timbre->FRMT[0]); // desconocido



         $string = '<TED version="1.0"><DD><RE>'.$RE.'</RE><TD>'.$TD.'</TD><F>'.$F.'</F><FE>'.$FE.'</FE><RR>'.$RR.'</RR><RSR>'.$RSR.'</RSR><MNT>'.$MNT.'</MNT><IT1>'.$MNT.'</IT1><CAF version="1.0"><DA><RE>'.$C_RE.'</RE><RS>'.$C_RS.'</RS><TD>39</TD><RNG><D>'.$C_RNG->D[0].'</D><H>'.$C_RNG->H[0].'</H></RNG><FA>'.$C_FA.'</FA><RSAPK><M>'.preg_replace('/\s+/', '',$C_RSAPK->M[0]).'</M><E>'.preg_replace('/\s+/', '',$C_RSAPK->E[0]).'</E></RSAPK><IDK>'.$IDK.'</IDK></DA><FRMA algoritmo="SHA1withRSA">'.trim($FRMA).'</FRMA></CAF><TSTED>'.trim($TSTED).'</TSTED></DD><FRMT algoritmo="SHA1withRSA">'.trim($FRMT).'</FRMT></TED>';











         // $string = '<TED version="1.0"><DD><RE>97975000-5</RE><TD>33</TD><F>60</F><FE>2003-10-13</FE><RR>77777777-7</RR><RSR>EMPRESA LTDA</RSR><MNT>119000</MNT><IT1>119000</IT1><CAF version="1.0"><DA><RE>97975000-5</RE><RS>RUT DE PRUEBA</RS><TD>39</TD><RNG><D>1</D><H>200</H></RNG><FA>2003-09-04</FA><RSAPK><M>0a4O6Kbx8Qj3K4iWSP4w7KneZYeJ+g/prihYtIEolKt3cykSxl1zO8vSXu397QhTmsX7SBEudTUx++2zDXBhZw==</M><E>Aw==</E></RSAPK><IDK>100</IDK></DA><FRMA algoritmo="SHA1withRSA">g1AQX0sy8NJugX52k2hTJEZAE9Cuul6pqYBdFxj1N17umW7zG/hAavCALKByHzdYAfZ3LhGTXCai5zNxOo4lDQ==</FRMA></CAF><TSTED>2003-10-13T09:33:20</TSTED></DD><FRMT algoritmo="SHA1withRSA">GbmDcS9e/jVC2LsLIe1iRV12Bf6lxsILtbQiCkh6mbjckFCJ7fj/kakFTS06Jo8iS4HXvJj3oYZuey53Krniew==</FRMT></TED>';
        // return $string;
            // $xml = simplexml_load_string($string);


            // <TED version="1.0">
            // <DD>
            // <RE>97975000-5</RE>
            // <TD>33</TD>
            // <F>60</F>
            // <FE>2003-10-13</FE>
            // <RR>77777777-7</RR>
            // <RSR>EMPRESA LTDA</RSR>
            // <MNT>119000</MNT>
            // <IT1>Parlantes Multimedia 180W.</IT1>
            // <CAF version="1.0">
            // <DA>
            // <RE>97975000-5</RE>
            // <RS>RUT DE PRUEBA</RS>
            // <TD>33</TD>
            // <RNG>
            // <D>1</D>
            // <H>200</H>
            // </RNG>
            // <FA>2003-09-04</FA>
            // <RSAPK>
            // </DA>
            // <FRMA
            // algoritmo="SHA1withRSA">g1AQX0sy8NJugX52k2hTJEZAE9Cuul6pqYBdFxj1N17umW7zG/hAavCA
            // LKByHzdYAfZ3LhGTXCai5zNxOo4lDQ==</FRMA>
            // </CAF>
            // </DD>

            // </TED>




        //Encode the data, returns a BarcodeData object
        // $pdf417 = new PDF417();
        // $data = $pdf417->encode($string);
        // Create a PNG image
        // $renderer = new SvgRenderer([
        //     'format' => 'jpg',
        //     'scale' => 20,
        //     'ratio' => 10
        // ]);

        // $img = $renderer->render($data);

        return response()->json([

            'caratula' => $xml->SetDTE->Caratula,
            'dte' => $xml->SetDTE->DTE,
            // 'img' => $img->encoded,
            'xml' => $string

        ]);

        // foreach ($xml->obra as $obra) {
        //     $elementoObra = array(); // Elemento que contendrá datos de cada nodo.
        //     $elementoObra["obra"] = trim((string)$obra); // Se agrega el contenido del nodo como cadena.
        //     $matrizDeObras[] = $elementoObra; // El elemento de cada nod es agregado a la matriz general.
        //     unset($elementoObra); // El contenido de un elemento nodo se recreará en cada iteración.
        // }
        // var_dump ($matrizDeObras);
    }


    function div_rut($rut){
        $obtener_rut_cliente = explode(" - ", $rut);
        $revers_rut_client = str_split(strrev($obtener_rut_cliente[1]));
        $i=1;
        $rut='';
        $dv='';
        foreach ($revers_rut_client as $key) {
            if($i == 1){

                $dv = $key;
            }else{

                $rut= $key.$rut;
            }

            $i++;
        }
        return $rut.'-'.$dv;
        // 188056520
    }
}
