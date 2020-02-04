<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Categoria extends Model
{
    protected $table = 'categoria';

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

    protected function registro_categoria($datos)
    {
        $validarDatos = $this->validar_categoria($datos);

        if ($validarDatos['estado'] == 'success') {
            $r = $this;
            $r->descripcion =  strtolower($datos->descripcion);
            $r->activo='S';

            if ($r->save()) {
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
                                    'id',
                                    'descripcion',
                                ])
                                    ->where('activo', 'S')
                                    ->orderby('id', 'asc')
                                    ->get();

        if (count($listar) > 0) {
            return $listar;
        } else {
            return "no hay categorias para mostrar";
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

    protected function modificar_campo_categoria($request)
    {
        $validarDatos = $this->validar_modificar_categoria($request);
       
        
        if ($validarDatos['estado'] == 'success') {
            $modificar = $this::find($request->id);

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
                                    'id',
                                    'descripcion',
                                ])
                                    ->where([
                                        'activo'=>'S',
                                    ])
                                    ->whereRaw("descripcion like lower('%$categoria%')")
                                    ->get();
        
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
