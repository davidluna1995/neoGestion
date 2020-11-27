<?php

namespace App\Http\Controllers;

use App\Caja;
use App\PeriodoCaja;
use App\RegistroCajaVendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeriodoController extends Controller
{


    public function verifica_existe_periodo(){

        //verifica un periodo activo
        $per_verifica = PeriodoCaja::where([
            'activo'=> 'S'
        ])->first();


        //verifica una caja
        $caj_verifica = RegistroCajaVendedor::where([
            'activo' => 'S',
            'user_id' => Auth::user()->id
        ])->first();

        if($per_verifica){ $periodo = ['estado' => 'ACTIVO']; } else { $periodo =['estado' => 'INACTIVO'];}

        if($caj_verifica){ $caja = ['estado' => 'ACTIVO'];  }else{ $caja = ['estado' => 'INACTIVO'];}


        //consultando la caja del usuario logeado
        $db_caja = DB::select("SELECT c.id caja_id, c.nombre from caja c
        inner join caja_user cu on c.id = cu.caja_id where cu.user_id =".Auth::user()->id);

        if(count($db_caja)>0){
            $get_caja = $db_caja;

        }else{

            $get_caja[0]= [
                'caja_id'=>'0',
                'nombre' => '(NO HAY CAJA ASIGNADA PARA ESTE USUARIO, ASIGNAR EN CONFIGURACIONES)'
            ];
        }

        return response()->json([
            'periodo' => $periodo,
            'caja' => $caja,
            'data_caja' => $get_caja[0],
        ]);


    }

    public function abrir_periodo_caja(Request $r){
        // dd($r->all());
        //consultar estado del periodo, Activo Inactivo o si aun no existe en DB

            //verifica un periodo activo
            $per_verifica = PeriodoCaja::where([
                'activo'=> 'S'
            ])->first();

            if($per_verifica){ // si el periodo esta activo,
                               //consultar la caja que el usuario quiere abrir, consultar su estado
                //verifica una caja
                $caj_verifica = RegistroCajaVendedor::where([
                    'activo' => 'S',
                    'user_id' => Auth::user()->id
                ])->first();

                if($caj_verifica){ // si la caja existe, no hacer nada mas
                    return ['estado'=>'failed_activo', 'mensaje'=> 'La caja y periodo ya estan activas'];
                }else{ // si la caja no existe, entonces iniciar mandando su monto inicio a caja y PERIODO
                    $caja = RegistroCajaVendedor::ingresar_con_periodo($r, $per_verifica->id);
                    if($caja == true){
                        return ['estado'=>'success', 'mensaje'=>'La caja se ha activado!'];
                    }
                    return ['estado'=>'failed_inactivo', 'mensaje'=>'La caja "NO" se ha activado'];

                }


            }else { //y si no esta activo el perido, se asume que no hay tampoco caja activa, entonces
                    // activar ambas (periodo y caja)

                $periodo = PeriodoCaja::abrir_periodo_mas_caja($r, Auth::user()->id);

                if($periodo == true){
                    return ['estado'=>'success', 'mensaje'=>'Periodo y caja se han activado!'];
                }
                return ['estado'=>'failed', 'mensaje'=>'Periodo y caja "NO" se han activado'];

            }

    }


    public function abrir_solo_caja(Request $r){



        //verificamos si el usuario tiene una caja activa, por defecto recibimos aqui un cero
        if($r->caja_id['caja_id'] == "0"){
            return [
                'estado' => 'failed',
                'mensaje'=>'No existe una caja asignada para su usuario, ir a configuraciones'
            ];
        }

        // primeramente si el periodo esta activo traer la informacion de este mismo
        //verifica un periodo activo
        $periodo = PeriodoCaja::where([
            'activo'=> 'S'
        ])->first();

        if($periodo){ // uuna vez obtenida la info del periodo, pasar a ver la caja

            // ver si la caja que se quiere abrir ya esta activa o no
            $verificar_estado_caja = RegistroCajaVendedor::where([
                'activo' => 'S',
                'caja_id' => $r->caja_id['caja_id']
            ])->first();

            if($verificar_estado_caja){ // si la caja esta activa, entonces no hacer nada

            return ['estado' => 'failed', 'mensaje'=>'Su caja asignada esta en uso por otro usuario', 'activo'=>'INACTIVO'];

            }else{ // si no existe actividad en la caja o si mi caja es otra, entonces abrir caja o unua caja
                $reg = new RegistroCajaVendedor();
                $reg->fecha_inicio = $r->fecha_inicio.' '.$r->hora_inicio;
                $reg->monto_inicio = $r->monto_inicio;
                $reg->activo = 'S';
                $reg->caja_id = $r->caja_id['caja_id'];
                $reg->user_id = Auth::user()->id;
                $reg->periodo_caja_id = $periodo->id;

                if($reg->save()){
                    $periodo->monto_inicio = (int)$periodo->monto_inicio + (int)$r->monto_inicio;
                    if($periodo->save()){
                        return ['estado'=>'success','mensaje'=>'La caja se ha activado', 'activo'=>'ACTIVO'];
                    }
                    return ['estado'=>'failed','mensaje'=>'No se ha activado la caja', 'activo'=>'INACTIVO'];
                }
                return ['estado'=>'failed','mensaje'=>'No se ha activado la caja', 'activo'=>'INACTIVO'];
            }
        }
        else{
            return ['estado'=>'failed_periodo', 'mensaje'=>'periodo no existe o no esta activo', 'activo'=>'INACTIVO'];
        }


    }


    public function cargar_datos_caja_y_o_periodo($caja_id){


        $TABLA = DB::select("SELECT
                            reg.id,
                            TO_CHAR(reg.fecha_inicio, 'DD/MM/YYYY HH24:MI') fecha_inicio,
                            TO_CHAR(reg.fecha_cierre,'DD/MM/YYYY HH24:MI') fecha_cierre,
                            reg.monto_inicio,
                            reg.monto_cierre,
                            reg.activo,
                            reg.periodo_caja_id,
                            u.name
                        from registro_caja_vendedor reg
                        inner join users u on u.id = reg.user_id
                        where caja_id = $caja_id and activo = 'S'");

        if(count($TABLA) > 0){
            return ['estado'=>'success', 'datos'=>$TABLA[0]];
        }
        return ['estado'=>'failed', 'datos'=>[]];

        //fecha_termino
    }

    public function cargar_datos_periodo(){

        //deberia traer al unico activo "S"
        $p = PeriodoCaja::where('activo','S')->first();

        if($p){
                $periodo_actual = DB::select("SELECT
                                        p.id,
                                        TO_CHAR(p.fecha_inicio, 'dd/mm/yyyy HH24:MI') fecha_inicio,
                                        TO_CHAR(p.fecha_cierre, 'dd/mm/yyyy HH24:MI') fecha_cierre,
                                        p.monto_inicio,
                                        p.monto_cierre,
                                        case
                                            when p.activo = 'S' then 'ACTIVA'
                                            when p.activo = 'N' then 'INACTIVA'
                                        END AS activo,
                                        p.inicio_user_id,
                                        u.name,
                                        x.monto_inicio monto_inicio_recalculado,
                                        x.monto_cierre monto_cierre_recalculado
                                    from periodo_caja p
                                    inner join users u on u.id = p.inicio_user_id
                                    inner join (
                                        select
                                            periodo_caja_id,
                                            sum(monto_inicio) monto_inicio,
                                            sum(monto_cierre) monto_cierre
                                        from registro_caja_vendedor where periodo_caja_id = $p->id
                                    group by periodo_caja_id limit 1) x on x.periodo_caja_id = p.id
                                    where p.activo = 'S'");



            if($periodo_actual){
                return ['estado'=>'success', 'almacenado_periodo'=>$periodo_actual[0], 'mensaje'=>'datos cargados'];
            }
            return ['estado'=>'failed', 'almacenado_periodo'=>[],'mensaje'=>'No hay periodo activo para cerrar'];
        }else{
            return ['estado'=>'failed', 'almacenado_periodo'=>[],'mensaje'=>'No hay periodo activo para cerrar'];
        }
    }

    public function cerrar_periodo($periodo_id, $estado_caja){
        // dd($periodo_id);

        if($estado_caja == 'ACTIVO'){
            return ['estado'=>'failed', 'mensaje'=> 'Su caja aún esta en estado ACTIVO, debe cerrar'];
        }

        date_default_timezone_set('America/Santiago');
        $fecha =date("Y-m-d H:i:s");
        $periodo = PeriodoCaja::find($periodo_id);

        if($periodo){
            $periodo->fecha_cierre = $fecha;
            $periodo->activo = 'N'; //CERRAR ACIVIDAD
            $periodo->cierre_user_id = Auth::user()->id;
            if($periodo->save()){
                return ['estado'=>'success', 'mensaje'=> 'Periodo cerrado correctamente'];
            }
            return ['estado'=>'failed', 'mensaje'=> 'No se ha podido cerrar el periodo'];
        }
        return ['estado'=>'failed', 'mensaje'=> 'No se ha encontrado el ID del periodo'];
    }

    public function captura_monto_cierre($r_c_v_id){

        // validar que el id de registro_caja_vendedor este activo nomas....
        $obtener_rcv =  RegistroCajaVendedor::where([
                            'id' => $r_c_v_id,
                            'activo' => 'S'
                        ])->first();



        // usuario quiere traer el monto de cierre total de SU caja ej:9
        $calculo = DB::select("SELECT
           COALESCE(sum(venta_total), 0) as suma_venta_total,
	       COALESCE(sum(cast(pago_debito AS INTEGER)), 0) as suma_pago_debito_total,
	       COALESCE(sum(cast(pago_efectivo AS INTEGER)) - sum(cast(vuelto AS INTEGER)), 0)  as suma_efectivo
        from ventas where registro_caja_vendedor_id =  $obtener_rcv->id");

        if(count($calculo)>0) return response()->json($calculo[0]); else return null;

    }


    public function cerrar_solo_caja(Request $r){

        if(trim($r->monto_cierre) == '' || ($r->rcv_id == 0 || $r->rcv_id=='' )){
            return ['estado' => 'failed', 'mensaje'=> 'Puede que falten datos para continuar'];
        }




        date_default_timezone_set('America/Santiago');
        $fecha =date("Y-m-d H:i:s");

        $reg = RegistroCajaVendedor::where([
            'activo'=>'S',
            'id' => $r->rcv_id
        ])->first();

        if($reg){
            $reg->monto_cierre = (int)$r->monto_cierre;
            $reg->fecha_cierre = $fecha;
            $reg->activo = 'N';

            if($reg->save()){
                $periodo = PeriodoCaja::find($reg->periodo_caja_id);
                $periodo->monto_cierre = $periodo->monto_cierre + $reg->monto_cierre;
                if($periodo->save()){
                    return ['estado' => 'success', 'mensaje'=> 'Caja cerrada!, periodo actualizado'];
                }
                return ['estado' => 'failed', 'mensaje'=> 'Caja cerrada, pero periodo no actualizado'];
            }
            return ['estado' => 'failed', 'mensaje'=> 'La caja no se ha cerrada'];
        }
        return ['estado' => 'failed', 'mensaje'=> 'No se puede continuar'];
    }
}
