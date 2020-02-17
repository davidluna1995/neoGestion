<?php

namespace App\Http\Controllers;

use App\Ventas;
use Carbon\Carbon;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Producto;

class VentasController extends Controller
{
    public function validar_venta($datos)
    {
        $validator = Validator::make(
            $datos->all(),
            [
                'producto_id' => 'required',
                'cantidad' => 'required',
                'venta' => 'required',
            ],
            [
                'producto_id.required' => 'El producto a ingresar es necesario',
                'cantidad.required' => 'La cantidad a ingresar es necesaria',
                'venta.required' => 'El monto de ventas a ingresar es necesario',
            ]
        );

 
        if ($validator->fails()) {
            return ['estado' => 'failed_v', 'mensaje' => $validator->errors()];
        }
        return ['estado' => 'success', 'mensaje' => 'success'];
    }

    protected function registro_venta(Request $datos)
    {
        $productoCantidad = Producto::select([

            'producto.cantidad',
            'producto.id',

        ])
            ->where('producto.id', $datos->producto_id)
            ->first();

        // dd($datos->cantidad > $productoCantidad);

        if ($datos->cantidad > $productoCantidad->cantidad) {
            return ['estado'=>'failed', 'mensaje'=>'Error, la cantidad de venta es mayor al stock.'];
        }

        $validarDatos = $this->validar_venta($datos);

        if ($validarDatos['estado'] == 'success') {
            $venta = new Ventas();
            $venta->user_id = Auth::user()->id;;
            $venta->producto_id = $datos->producto_id;
            $venta->cantidad = $datos->cantidad;
            $venta->venta = $datos->venta;
            $venta->forma_pago_id = '1';
            $venta->tipo_entrega_id = '1';

            if ($venta->save()) {
                $actualizarCantidad = Producto::find($datos->producto_id);
                $actualizarCantidad->cantidad = $actualizarCantidad->cantidad - $datos->cantidad;
                if ($actualizarCantidad->save()) {
                    return ['estado'=>'success', 'mensaje'=>'Venta guardada con exito, Actualizando nuevo stock'];
                }
            } else {
                return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error, verifique esten correctos los campo.'];
            }
        }
        return $validarDatos;
    }

    protected function traer_ventas()
    {
        $listar = Ventas::select([
                                    'ventas.id',
                                    'producto.nombre',
                                    'categoria.descripcion as catDesc',
                                    'producto.descripcion as proDesc',
                                    'ventas.cantidad',
                                    'ventas.created_at as creado',
                                    'ventas.venta',
                                    'producto.precio_venta',
                                    'categoria.id as catId',
                                ])
                                    ->join('producto', 'producto.id', 'ventas.producto_id')
                                    ->join('categoria', 'categoria.id', 'producto.categoria_id')
                                    ->orderby('ventas.id', 'desc')
                                    ->get();
        if (count($listar) > 0) {
            foreach ($listar as $key) {
                setlocale(LC_TIME, 'es');
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
                                    'ventas.id',
                                    'producto.nombre',
                                    'categoria.descripcion as catDesc',
                                    'producto.descripcion as proDesc',
                                    'ventas.cantidad',
                                    'ventas.created_at as creado',
                                    'ventas.venta',
                                    'producto.precio_venta',
                                    'categoria.id as catId',
                                ])
                                    ->join('producto', 'producto.id', 'ventas.producto_id')
                                    ->join('categoria', 'categoria.id', 'producto.categoria_id')
                                    ->where([
                                        'ventas.activo'=>'S',
                                    ])
                                    ->whereRaw(
                                        "producto.nombre like lower('%$producto%') or 
                                        categoria.descripcion like lower('%$producto%') or
                                        producto.descripcion like lower('%$producto%')"
                                    )

                                    ->get();
        
        if (!$listar->isEmpty()) {
            return ['estado'=>'success' , 'producto' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'El producto no existe en nuestros registros, refrescando la tabla.'];
        }
    }

    protected function total_ventas()
    {
        $listar = DB::table('ventas')->sum('venta');
        if ($listar > 0) {
            return $listar;
        } else {
            return 0;
        }
    }

    protected function ultimas_ventas()
    {
        $listar = Ventas::select([
                                    'ventas.id',
                                    'producto.nombre',
                                    'categoria.descripcion as catDesc',
                                    'producto.descripcion as proDesc',
                                    'ventas.cantidad',
                                    'ventas.created_at as creado',
                                    'ventas.venta',
                                    'producto.precio_venta',
                                    'categoria.id as catId',
                                ])
                                    ->join('producto', 'producto.id', 'ventas.producto_id')
                                    ->join('categoria', 'categoria.id', 'producto.categoria_id')
                                    ->orderby('ventas.id', 'desc')
                                    ->take(5)
                                    ->get();
        if (count($listar) > 0) {
            foreach ($listar as $key) {
                setlocale(LC_TIME, 'es');
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
            " SELECT * from 
           (select 
            producto_id,
            p.nombre
            from ventas
            inner join producto p on p.id = ventas.producto_id
            group by producto_id, p.nombre) producto
            
            inner join 
            (select producto_id, 
            sum(cantidad) as cantidad_total, 
            sum(venta) as venta_total 
            from ventas 
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
}
