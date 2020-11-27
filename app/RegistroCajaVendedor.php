<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\PeriodoCaja;

class RegistroCajaVendedor extends Model
{
    protected $table = "registro_caja_vendedor";


    protected function ingresar_con_periodo($r, $periodo_caja_id){

        $user = Auth::user()->id;

        $reg = $this;
        $reg->fecha_inicio = $r->fecha_inicio.' '.$r->hora_inicio;
        $reg->monto_inicio = $r->monto_inicio;
        $reg->activo = 'S';
        $reg->caja_id = $r->caja_id['caja_id'];
        $reg->user_id = Auth::user()->id;
        $reg->periodo_caja_id = $periodo_caja_id;

        if($reg->save()){ // al registrar datos en la caja pasar tambien PERIODO
            $p = PeriodoCaja::find($periodo_caja_id);
            $p->fecha_inicio = $r->fecha_inicio.' '.$r->hora_inicio;
            $p->monto_inicio = $r->monto_inicio;
            $p->activo = 'S';
            $p->inicio_user_id = Auth::user()->id;

            if($p->save()){
                return true;
            }
            return false;

        }
        return false;

    }
}
