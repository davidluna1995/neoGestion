<?php

namespace App\Http\Controllers;

use App\Configuraciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ConfiguracionesController extends Controller
{
    public function validar_configuraciones($datos)
    {
        $validator = Validator::make(
            $datos->all(),
            [
                // 'logo' => 'file|mimes:png,jpg',
            ],
            [
                // 'logo.file' => 'Lo seleccionado debe ser un archivo',
                // 'logo.mimes' => 'El formato del archivo debe ser PNG o JPG',
            ]
        );

 
        if ($validator->fails()) {
            return ['estado' => 'failed_v', 'mensaje' => $validator->errors()];
        }
        return ['estado' => 'success', 'mensaje' => 'success'];
    }

    public function registro_configuraciones(Request $datos)
    {
        $validarDatos = $this->validar_configuraciones($datos);
        if ($validarDatos['estado'] == 'success') {
            $traer = $this->traer_configuraciones();
            if ($traer['estado'] == 'success') {

                $ruta = substr($traer['configuraciones']->logo, 8);
                $borrar = Storage::delete($ruta);

                $update = Configuraciones::find($traer['configuraciones']->id);

                $update->empresa = $datos->empresa;
                $update->direccion = $datos->direccion;
                if ($datos->logo != 'undefined') {
                    $guardarArchivo = $this->guardarArchivo($datos->logo, 'ArchivosConfiguracion/');
                    if ($guardarArchivo['estado'] == "success") {
                        $update->logo = $guardarArchivo['archivo'];
                    } else {
                        return $guardarArchivo;
                    }
                }


                if ($update->save()) {
                    return ['estado'=>'success', 'mensaje'=>'Información guardada con éxito.'];
                } else {
                    return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error, verifique esten correcto los campos.'];
                }
            } else {
                $conf = new Configuraciones();
                $conf->empresa = $datos->empresa;
                $conf->direccion = $datos->direccion;

                if ($datos->logo != 'undefined') {
                    $guardarArchivo = $this->guardarArchivo($datos->logo, 'ArchivosConfiguracion/');
                    if ($guardarArchivo['estado'] == "success") {
                        $conf->logo = $guardarArchivo['archivo'];
                    } else {
                        return $guardarArchivo;
                    }
                }

                if ($conf->save()) {
                    return ['estado'=>'success', 'mensaje'=>'Información guardada con éxito.'];
                } else {
                    return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error, verifique esten correcto los campos.'];
                }
            }
        }
        return $validarDatos;
    }

    public function traer_configuraciones()
    {
        $listar = Configuraciones::select([
                                    'id',
                                    'logo',
                                    'empresa',
                                    'direccion',
                                ])
                                    ->first();

        if (!is_null($listar)) {
            return ['estado'=>'success' , 'configuraciones' => $listar];
        } else {
            return ['estado'=>'failed', 'mensaje'=>'No existe informacion.'];
        }
    }

    public function guardarArchivo($archivo, $ruta)
    {
        $filenameext = $archivo->getClientOriginalName();
        $filename = pathinfo($filenameext, PATHINFO_FILENAME);
        $extension = $archivo->getClientOriginalExtension();
        $nombreArchivo = $filename . '_' . time() . '.' . $extension;
        $rutaDB = 'storage/' . $ruta . $nombreArchivo;
        $guardar = Storage::put($ruta . $nombreArchivo, (string) file_get_contents($archivo), 'public');
        if ($guardar) {
            return ['estado' =>  'success', 'archivo' => $rutaDB];
        } else {
            return ['estado' =>  'failed', 'mensaje' => 'Error al intentar guardar el archivo.'];
        }
    }
}
