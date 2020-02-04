<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Producto;

class Ventas extends Model
{
    protected $table = 'ventas';


    public function validar_venta($datos)
    {
        $validator = Validator::make(
            $datos->all(),
            [
                'producto_id' => 'required',
                'fecha' => 'required',
                'hora' => 'required',
                'cantidad' => 'required',
                'venta' => 'required',
            ],
            [
                'producto_id.required' => 'El producto a ingresar es necesario',
                'fecha.required' => 'La fecha a ingresar es necesaria',
                'hora.required' => 'La hora a ingresar es necesaria',
                'cantidad.required' => 'La cantidad a ingresar es necesaria',
                'venta.required' => 'El monto de ventas a ingresar es necesario',
            ]
        );

 
        if ($validator->fails()) {
            return ['estado' => 'failed_v', 'mensaje' => $validator->errors()];
        }
        return ['estado' => 'success', 'mensaje' => 'success'];
    }

    protected function registro_venta($datos)
    {
        $productoCantidad = Producto::select([

            'producto.cantidad',
            'producto.id',

        ])
            ->where('producto.activo', 'S')
            ->where('producto.id', $datos->producto_id)
            ->first();

        // dd($datos->cantidad > $productoCantidad);

        if ($datos->cantidad > $productoCantidad->cantidad) {
            return ['estado'=>'failed', 'mensaje'=>'Error, la cantidad de venta es mayor al stock.'];
        }

        $validarDatos = $this->validar_venta($datos);

        if ($validarDatos['estado'] == 'success') {
            $r = $this;
            $r->producto_id = $datos->producto_id;
            $r->fecha = $datos->fecha;
            $r->hora = $datos->hora;
            $r->cantidad = $datos->cantidad;
            $r->venta = $datos->venta;
            $r->activo='S';

            if ($r->save()) {
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
                                    'ventas.fecha as fechaVenta',
                                    'ventas.hora as horaVenta',
                                    'ventas.venta',
                                    'producto.precio_venta',
                                    'categoria.id as catId',
                                ])
                                    ->join('producto', 'producto.id', 'ventas.producto_id')
                                    ->join('categoria', 'categoria.id', 'producto.categoria_id')
                                    ->where('ventas.activo', 'S')
                                    ->orderby('ventas.id', 'desc')
                                    ->get();
        if (count($listar) > 0) {
            foreach ($listar as $key) {
                setlocale(LC_TIME, 'es');
                $key->fechaVenta = Carbon::parse($key->fechaVenta)->formatLocalized('%d de %B del %Y');
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
                                    'ventas.fecha as fechaVenta',
                                    'ventas.hora as horaVenta',
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
}
