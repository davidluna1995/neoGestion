<?php

namespace App\Http\Controllers;

use App\Categoria;
use Carbon\Carbon;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{

    public function validar_categoria($datos)
    {
        $validator = Validator::make(
            $datos->all(),
            [
                'descripcion' => 'required|unique:categoria',
            ],
            [
                'descripcion.required' => 'La categoria a ingresar es necesaria',
                'descripcion.unique' => 'La categoria ingresada ya existe en nuestros registros.'
            ]
        );

 
        if ($validator->fails()) {
            return ['estado' => 'failed_v', 'mensaje' => $validator->errors()];
        }
        return ['estado' => 'success', 'mensaje' => 'success'];
    }

    protected function registro_categoria(Request $datos)
    {
        $validarDatos = $this->validar_categoria($datos);

        if ($validarDatos['estado'] == 'success') {
            $categoria = new Categoria();
            $categoria->user_id = '1';
            $categoria->descripcion =  strtolower($datos->descripcion);

            if ($categoria->save()) {
                return ['estado'=>'success', 'mensaje'=>'Categoria guardada con exito.'];
            } else {
                return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error, verifique este correcto el campo.'];
            }
        }
        return $validarDatos;
    }

    protected function traer_categorias()
    {
        $listar = Categoria::select([
                                    'categoria.id',
                                    'categoria.descripcion',
                                    'categoria.created_at as creado',
                                    'u.name as nombreUsuario'
                                ])
                                    ->join('users as u','u.id','categoria.user_id')
                                    ->orderby('id', 'desc')
                                    ->get();

        // dd($listar);

        if (count($listar) > 0) {
            foreach ($listar as $key) {
                setlocale(LC_TIME, 'es');
                $key->creado = Carbon::parse($key->creado)->formatLocalized('%d de %B del %Y %H:%M:%S');
            }
        }
        if (!$listar->isEmpty()) {
            return ['estado'=>'success' , 'cat' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'No existen ventas.'];
        }
    }

    protected function validar_modificar_categoria($request)
    {
        switch ($request->campo) {
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

        default:
        return null;
          break;
      }
        if ($validator->fails()) {
            return ['estado' => 'failed_v', 'mensaje' => $validator->errors()];
        }
        return ['estado' => 'success', 'mensaje' => 'success'];
    }

    protected function modificar_campo_categoria(Request $request)
    {
        $validarDatos = $this->validar_modificar_categoria($request);
       
        
        if ($validarDatos['estado'] == 'success') {
            $modificar = Categoria::find($request->id);

            if (!is_null($modificar)) {
                switch ($request->campo) {

                case 'descripcion':

                  $validar = strtolower($request->input);
                  $verificarDescripcion = Categoria::select([
                    'descripcion',
                ])
                                        ->where('descripcion', $validar)
                                        ->get();
                    if (count($verificarDescripcion) > 0) {
                        return ['estado'=>'failed', 'mensaje'=>'La categoria ya existe en los registros.'];
                    }

                        $modificar->descripcion = $validar;

                        if ($modificar->save()) {
                            return ['estado'=>'success', 'mensaje'=>'Categoria actualizada.'];
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

    protected function buscar_categoria($categoria)
    {
        $listar = Categoria::select([
                                    'categoria.id',
                                    'categoria.descripcion',
                                    'categoria.created_at as creado',
                                    'u.name as nombreUsuario'
                                    ])
                                    ->join('users as u','u.id','categoria.user_id')
                                    ->whereRaw("categoria.descripcion like lower('%$categoria%')")
                                    ->get();

        if (count($listar) > 0) {
            foreach ($listar as $key) {
                setlocale(LC_TIME, 'es');
                $key->creado = Carbon::parse($key->creado)->formatLocalized('%d de %B del %Y %H:%M:%S');
            }
        }
        
        if (!$listar->isEmpty()) {
            return ['estado'=>'success' , 'categoria' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'La categoria no existe en nuestros registros.'];
        }
    }
    protected function cantidad_categoria()
    {
        $listar = DB::table('categoria')->count();
        if ($listar > 0) {
            return $listar;
        } else {
            return 0;
        }
    }
}
