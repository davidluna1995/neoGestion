<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\RegistroCajaVendedor;
class PeriodoCaja extends Model
{
    protected $table = "periodo_caja";


    protected function abrir_periodo_mas_caja($r, $user){

        $p = $this;

        $p->fecha_inicio = $r->fecha_inicio.' '.$r->hora_inicio;
        $p->monto_inicio = $r->monto_inicio;
        $p->activo = 'S';
        $p->inicio_user_id = $user;

        if($p->save()){
            $reg = new RegistroCajaVendedor();
            $reg->fecha_inicio = $r->fecha_inicio.' '.$r->hora_inicio;
            $reg->monto_inicio = $r->monto_inicio;
            $reg->activo = 'S';
            $reg->caja_id = $r->caja_id['caja_id'];
            $reg->user_id = Auth::user()->id;
            $reg->periodo_caja_id = $p->id;

            if($reg->save()){
                return true;
            }
            return false;
        }

        return false;


    }
}
