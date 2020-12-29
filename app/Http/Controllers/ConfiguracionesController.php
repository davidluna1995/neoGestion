<?php

namespace App\Http\Controllers;

use App\Configuraciones;
use App\Caja;
use App\CajaUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

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
    function valida_rut($rut)
    {
        try{
            $rut = preg_replace('/[^k0-9]/i', '', $rut);
            $dv  = substr($rut, -1);
            $numero = substr($rut, 0, strlen($rut)-1);
            $i = 2;
            $suma = 0;
            foreach(array_reverse(str_split($numero)) as $v)
            {
                if($i==8)
                    $i = 2;
                $suma += $v * $i;
                ++$i;
            }
            $dvr = 11 - ($suma % 11);

            if($dvr == 11)
                $dvr = 0;
            if($dvr == 10)
                $dvr = 'K';
            if($dvr == strtoupper($dv))
                return true;
            else
                return false;
        }
        catch(\Exception $e){
            return false;
        }
    }

    // public function registro_configuraciones(Request $datos)
    // {
    //     $validarDatos = $this->validar_configuraciones($datos);
    //     if ($validarDatos['estado'] == 'success') {
    //         $traer = $this->traer_configuraciones();
    //         if ($traer['estado'] == 'success') {

    //             $ruta = substr($traer['configuraciones']->logo, 8);
    //             $borrar = Storage::delete($ruta);

    //             $update = Configuraciones::find($traer['configuraciones']->id);

    //             $update->empresa = $datos->empresa;
    //             $update->direccion = $datos->direccion;
    //             if ($datos->logo != 'undefined') {
    //                 $guardarArchivo = $this->guardarArchivo($datos->logo, 'ArchivosConfiguracion/');
    //                 if ($guardarArchivo['estado'] == "success") {
    //                     $update->logo = $guardarArchivo['archivo'];
    //                 } else {
    //                     return $guardarArchivo;
    //                 }
    //             }


    //             if ($update->save()) {
    //                 return ['estado' => 'success', 'mensaje' => 'Información guardada con éxito, por seguridad la sesión cerrará automaticamente.'];
    //             } else {
    //                 return ['estado' => 'failed', 'mensaje' => 'A ocurrido un error, verifique esten correcto los campos.'];
    //             }
    //         } else {
    //             $conf = new Configuraciones();
    //             $conf->empresa = $datos->empresa;
    //             $conf->direccion = $datos->direccion;

    //             if ($datos->logo != 'undefined') {
    //                 $guardarArchivo = $this->guardarArchivo($datos->logo, 'ArchivosConfiguracion/');
    //                 if ($guardarArchivo['estado'] == "success") {
    //                     $conf->logo = $guardarArchivo['archivo'];
    //                 } else {
    //                     return $guardarArchivo;
    //                 }
    //             }

    //             if ($conf->save()) {
    //                 return ['estado' => 'success', 'mensaje' => 'Información guardada con éxito, por seguridad la sesión cerrará automaticamente.'];
    //             } else {
    //                 return ['estado' => 'failed', 'mensaje' => 'A ocurrido un error, verifique esten correcto los campos.'];
    //             }
    //         }
    //     }
    //     return $validarDatos;
    // }

    public function registro_configuraciones(Request $datos)
    {


        $validarDatos = $this->validar_configuraciones($datos);
        if ($validarDatos['estado'] == 'success') {

            if($this->valida_rut($datos->rut)){
                $traer = $this->traer_configuraciones();
                if ($traer['estado'] == 'success') {

                    $ruta = substr($traer['configuraciones']->logo, 8);
                    $borrar = Storage::delete($ruta);

                    $update = Configuraciones::find($traer['configuraciones']->id);

                    $update->empresa = $datos->empresa;
                    $update->direccion = $datos->direccion;
                    $update->rut = $datos->rut;


                    $update->rut = $datos->rut;

                    if ($datos->logo != 'undefined') {
                        $guardarArchivo = $this->guardarArchivo($datos->logo, 'ArchivosConfiguracion/');
                        if ($guardarArchivo['estado'] == "success") {
                            $update->logo = $guardarArchivo['archivo'];
                        } else {
                            return $guardarArchivo;
                        }
                    }


                    if ($update->save()) {
                        return ['estado'=>'success', 'mensaje'=>'Información guardada con éxito, por seguridad la sesión cerrará automaticamente.'];
                    } else {
                        return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error, verifique esten correcto los campos.'];
                    }
                } else {
                    $conf = new Configuraciones();
                    $conf->empresa = $datos->empresa;
                    $conf->direccion = $datos->direccion;
                    $conf->rut = $datos->rut;
                    if ($datos->logo != 'undefined') {
                        $guardarArchivo = $this->guardarArchivo($datos->logo, 'ArchivosConfiguracion/');
                        if ($guardarArchivo['estado'] == "success") {
                            $conf->logo = $guardarArchivo['archivo'];
                        } else {
                            return $guardarArchivo;
                        }
                    }

                    if ($conf->save()) {
                        return ['estado'=>'success', 'mensaje'=>'Información guardada con éxito, por seguridad la sesión cerrará automaticamente.'];
                    } else {
                        return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error, verifique esten correcto los campos.'];
                    }
                }
            }else{
                return ['estado'=>'failed', 'mensaje'=>'rut no valido'];
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
            'rut',
            'giro'
        ])->first();

        if (!is_null($listar)) {
            return [ 'estado' => 'success', 'configuraciones' => $listar ];
        } else {
            return ['estado' => 'failed', 'mensaje' => 'No existe informacion.'];
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

    public function ingresar_caja(Request $r)
    {

        try {
            $caja = new Caja;
            $caja->nombre = $r->nombre;
            $caja->descripcion = $r->descripcion;
            $caja->activo = 'N';
            $caja->user_crea = Auth::user()->id;

            if ($caja->save()) {
                return [
                    'estado' => 'success',
                    'mensaje' => 'Caja ingresada con exito'
                ];
            }
            return [
                'estado' => 'failed',
                'mensaje' => 'No se ha podido seguir con el proceso'
            ];
        } catch (QueryException $e) {
            // DB::rollBack();
            return [
                'estado'  => 'failed',
                'mensaje' => 'QEx: No se ha podido seguir con el proceso de guardado, intente nuevamente o verifique sus datos',
                'error' => $e
            ];
        } catch (\Exception $e) {
            // DB::rollBack();
            return [
                'estado'  => 'failed',
                'mensaje' => 'Ex: No se ha podido seguir con el proceso de guardado, intente nuevamente o verifique sus datos',
                'error' => $e
            ];
        }
    }


    public function traer_cajas(Request $r){

        // $tabla = DB::select("SELECT
        //     c.id,
        //     c.nombre,
        //     c.descripcion,
        //     case
        //         when c.activo = 'N' then 'Inactiva'
        //         when c.activo = 'S' then 'Activa'
        //     end as activo,
        //     name
        // from caja c
        // inner join users u on u.id = c.user_crea");

        $tabla = DB::select("SELECT * from
        (
            select
                c.id, -- id de la caja
                c.nombre,
                c.descripcion,
                to_char(reg.fecha_inicio, 'DD/MM/YYYY HH24:MI') fecha_inicio_r_c_v,
                to_char(reg.fecha_cierre, 'DD/MM/YYYY HH24:MI') fecha_cierre_r_c_v,
                reg.monto_inicio monto_inicio_r_c_v,
                reg.user_id user_activo_id,
                u.name user_activo,
                uc.id user_crea_id,
                uc.name user_crea,
                case
                    when reg.activo = 'S' then 'ACTIVA'
                    when reg.activo IS NULL then  'INACTIVA'
                end as activo
            from caja c
        left join registro_caja_vendedor reg on c.id = reg.caja_id and reg.activo = 'S'
        left join users u on u.id = reg.user_id
        left join users uc on uc.id = c.user_crea

        order by c.id asc) x");

            if(count($tabla) > 0){
                return [
                    'estado' => 'success',
                    'tabla' => $tabla
                ];
            }else{
                return [
                    'estado' => 'failed',
                    'tabla' => [],
                ];
            }

    }



    public function editar_caja(Request $r){

        if(trim($r->nombre) == ''){
            return ['estado'=>'failed', 'mensaje'=>'El nombre es obligatorio'];
        }
        if(is_null($r->descripcion)){
            $r->descripcion = '';
        }

        $caja = Caja::find($r->id);
        $caja->nombre = $r->nombre;
        $caja->descripcion = $r->descripcion;

        if($caja->save()){
            return ['estado'=>'success','mensaje'=>'Datos actualizados'];
        }
        return ['estado'=>'failed','mensaje'=>'No se ha podido actualizar los datos'];
    }


    public function asignar_usuario_a_caja(Request $r){

        try{
                $verify = CajaUser::where([
                            'caja_id' => $r->caja_id,
                            'user_id' => $r->usuario_id,
                            'activo' => 'S'
                        ])->first();

                if($verify){

                    return ['estado'=>'failed', 'mensaje'=>'El usuario ya tiene asignada esta caja'];

                }

                $verify2 = CajaUser::where([
                            'user_id' => $r->usuario_id,
                            'activo' => 'S'
                        ])->first();

                if($verify2){
                    return ['estado'=>'failed', 'mensaje'=>'El usuario ya tiene asignada una caja'];
                }


                    $cu = new CajaUser;
                    $cu->caja_id = $r->caja_id;
                    $cu->user_id = $r->usuario_id;
                    $cu->activo = 'S';
                    if($cu->save()){
                        return ['estado'=>'success', 'mensaje'=>'Caja asignada!'];
                    }
                    return ['estado'=>'failed', 'mensaje'=>'No se ha podido continuar con el proceso'];

        } catch (QueryException $e) {
            // DB::rollBack();
            return [
                'estado'  => 'failed',
                'mensaje' => 'QEx: No se ha podido seguir con el proceso de guardado, intente nuevamente o verifique sus datos',
                'error' => $e
            ];
        } catch (\Exception $e) {
            // DB::rollBack();
            return [
                'estado'  => 'failed',
                'mensaje' => 'Ex: No se ha podido seguir con el proceso de guardado, intente nuevamente o verifique sus datos',
                'error' => $e
            ];
        }


    }

    public function ver_usuarios_en_caja($caja_id){

        return CajaUser::ver_usuarios_en_caja($caja_id);

    }




    public function codificar_xml(Request $r)
    {
        // dd($r->xml);
        $codific = base64_encode($r->xml);
        $test1 = str_replace(' ', '%20', $r->xml);
        $test2 = (preg_replace('/\s+/', '', $r->xml));

        $tes1_code = base64_encode($test1);
        $tes1_decode = \base64_decode($tes1_code);

        return [
            $test1, $test2, $tes1_code, $tes1_decode
        ];

        return [
            'caf_xml_normal' => $r->xml,
            'caf_xml_codificado' => base64_encode($r->xml),
            'caf_xml_sin_+_cod' => (preg_replace('/\s+/', '', $r->xml))
        ];
    }
}
