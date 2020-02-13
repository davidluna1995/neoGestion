<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Categoria;

class Producto extends Model
{
    use softDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'producto';

    public function validar_producto($datos)
    {
        $validator = Validator::make(
            $datos->all(),
            [
                'categoria_id' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required',
                'cantidad' => 'required',
                'precio_compra' => 'required',
                'precio_venta' => 'required',
            ],
            [
                'categoria_id.required' => 'La categoria a ingresar es necesaria',
                'nombre.required' => 'El nombre a ingresar es necesario',
                'descripcion.required' => 'La descripcion a ingresar es necesaria',
                'cantidad.required' => 'La cantidad a ingresar es necesaria',
                'precio_compra.required' => 'El precio de compra a ingresar es necesario',
                'precio_venta.required' => 'el precio de venta a ingresar es necesario',
            ]
        );

 
        if ($validator->fails()) {
            return ['estado' => 'failed_v', 'mensaje' => $validator->errors()];
        }
        return ['estado' => 'success', 'mensaje' => 'success'];
    }

    protected function registro_producto($datos)
    {
        $validarDatos = $this->validar_producto($datos);

        if ($validarDatos['estado'] == 'success') {
            $r = $this;
            $r->categoria_id = $datos->categoria_id;
            $r->nombre =  strtolower($datos->nombre);
            $r->descripcion = strtolower($datos->descripcion);
            $r->cantidad = $datos->cantidad;
            $r->precio_compra = $datos->precio_compra;
            $r->precio_venta = $datos->precio_venta;

            if ($r->save()) {
                return ['estado'=>'success', 'mensaje'=>'Producto guardado con exito.'];
            } else {
                return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error, verifique esten correctos los campo.'];
            }
        }
        return $validarDatos;
    }

    protected function traer_productos()
    {
        $listar = Producto::select([
                                    'producto.id',
                                    'producto.nombre',
                                    'producto.descripcion as proDesc',
                                    'producto.cantidad',
                                    'producto.precio_compra',
                                    'producto.precio_venta',
                                    'categoria.descripcion as catDesc',
                                    'categoria.id as catId',
                                    'producto.created_at as creado',
                                ])
                                    ->join('categoria', 'categoria.id', 'producto.categoria_id')
                                    // ->orderby('producto.id', 'asc')
                                    ->get();
        if (count($listar) > 0) {
            foreach ($listar as $key) {
                setlocale(LC_TIME, 'es');
                $key->creado = Carbon::parse($key->creado)->formatLocalized('%d de %B del %Y %H:%M:%S');
            }
        }
        if (!$listar->isEmpty()) {
            return ['estado'=>'success' , 'productos' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'No existen productos.'];
        }
    }

    protected function validar_modificar_producto($request)
    {
        switch ($request->campo) {
        case 'categoria_id':
          $validator = Validator::make(
              $request->all(),
              [
              'input' => 'required',
            ],
              [
              'input.required' => 'Debe ingresar una categoria.',
            ]
          );
          break;

        case 'nombre':
          $validator = Validator::make(
              $request->all(),
              [
              'input' => 'required',
            ],
              [
              'input.required' => 'Debe ingresar un nombre.',
            ]
          );
          break;

        case 'descripcion':
          $validator = Validator::make(
              $request->all(),
              [
              'input' => 'required',
            ],
              [
              'input.required' => 'Debe ingresar una descripcion.',
            ]
          );
          break;

        case 'cantidad':
          $validator = Validator::make(
              $request->all(),
              [
              'input' => 'required',
            ],
              [
              'input.required' => 'Debe ingresar una cantidad.',
            ]
          );
          break;

        case 'precio_compra':
          $validator = Validator::make(
              $request->all(),
              [
              'input' => 'required',
            ],
              [
              'input.required' => 'Debe ingresar un precio de compra.',
            ]
          );
          break;
        case 'precio_venta':
          $validator = Validator::make(
              $request->all(),
              [
              'input' => 'required',
            ],
              [
              'input.required' => 'Debe ingresar un precio de venta.',
            ]
          );
          break;

        default:
        return null;
          break;
      }
        if ($validator->fails()) {
            return ['estado' => 'failed_v', 'mensaje' => $validator->errors()];
        }
        return ['estado' => 'success', 'mensaje' => 'success'];
    }

    protected function modificar_campo_producto($request)
    {
        $validarDatos = $this->validar_modificar_producto($request);


       
        
        if ($validarDatos['estado'] == 'success') {
            $modificar = $this::find($request->id);

            if (!is_null($modificar)) {
                switch ($request->campo) {

                case 'categoria_id':
   
                        $modificar->categoria_id = $request->input;

                        if ($modificar->save()) {
                            return ['estado'=>'success', 'mensaje'=>'Categoria actualizada.'];
                        } else {
                            return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error al igreso de datos.'];
                        }
                    
                  break;

                case 'nombre':

                  $validar = strtolower($request->input);
                  $verificarCategoria = Producto::select([
                    'nombre',
                ])
                                        ->where('nombre', $validar)
                                        ->get();
                    if (count($verificarCategoria) > 0) {
                        return ['estado'=>'failed', 'mensaje'=>'El producto ya existe en nuestros registros.'];
                    }

                        $modificar->nombre = $validar;

                        if ($modificar->save()) {
                            return ['estado'=>'success', 'mensaje'=>'Producto actualizado.'];
                        } else {
                            return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error al igreso de datos.'];
                        }
                    
                  break;

                  
                case 'descripcion':
   
                    $modificar->descripcion = $request->input;

                    if ($modificar->save()) {
                        return ['estado'=>'success', 'mensaje'=>'Descripcion actualizada.'];
                    } else {
                        return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error al igreso de datos.'];
                    }
                
              break;

                case 'cantidad':
              
                      $modificar->cantidad = $request->input;
                      if ($modificar->save()) {
                          return ['estado'=>'success', 'mensaje'=>'Cantidad actualizada.'];
                      } else {
                          return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error al igreso de datos.'];
                      }
              break;

                case 'precio_compra':
   
                    $modificar->precio_compra = $request->input;

                    if ($modificar->save()) {
                        return ['estado'=>'success', 'mensaje'=>'Precio de compra actualizado.'];
                    } else {
                        return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error al igreso de datos.'];
                    }
                
              break;

                case 'precio_venta':
   
                    $modificar->precio_venta = $request->input;

                    if ($modificar->save()) {
                        return ['estado'=>'success', 'mensaje'=>'Precio de venta actualizado.'];
                    } else {
                        return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error al igreso de datos.'];
                    }
                
              break;
     
                default:
                  return null;
                  break;
              }
            }
            return ['estado'=>'failed', 'mensaje'=>'El item que intentas modificar no existe.'];
        }
        return $validarDatos;
    }

    protected function cantidad_productos()
    {
        $listar = DB::table('producto')->count();
        if ($listar > 0) {
            return $listar;
        } else {
            return 0;
        }
    }

    protected function buscar_producto($producto)
    {
        $listar = Producto::select([
                                    'producto.id',
                                    'producto.nombre',
                                    'producto.descripcion as proDesc',
                                    'producto.cantidad',
                                    'producto.precio_compra',
                                    'producto.precio_venta',
                                    'producto.created_at as creado',
                                    'categoria.descripcion as catDesc',
                                    'categoria.id as catId',
                                ])
                                    ->join('categoria', 'categoria.id', 'producto.categoria_id')
                                    ->whereRaw(
                                        "producto.nombre like lower('%$producto%') or 
                                      categoria.descripcion like lower('%$producto%') or
                                      producto.descripcion like lower('%$producto%')"
                                    )
                                    ->get();

        if (count($listar) > 0) {
            foreach ($listar as $key) {
                setlocale(LC_TIME, 'es');
                $key->creado = Carbon::parse($key->creado)->formatLocalized('%d de %B del %Y %H:%M:%S');
            }
        }
        
        if (!$listar->isEmpty()) {
            return ['estado'=>'success' , 'producto' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'El producto no existe en nuestros registros,refrescando la tabla.'];
        }
    }
}
