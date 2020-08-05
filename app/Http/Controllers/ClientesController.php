<?php

namespace App\Http\Controllers;

use App\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientesController extends Controller
{
    public function guardar(Request $r)
    {
        
        $nombres = ucwords($r->nombres);
        $apellidos = ucwords($r->apellidos);

        
        if($this->validadorRut($r->rut)){
            $rut = (string)$r->rut;
            $verify = Cliente::whereRaw("upper(rut) = upper('$rut')")->first();
            if($verify){
                return ['estado'=>'failed','mensaje'=>'El rut ya estan en uso'];
            }else{
                $c = new Cliente;
                $c->rut = strtolower($r->rut);
                $c->nombres = $nombres;
                $c->apellidos = $apellidos;
                $c->contacto = $r->contacto;
                $c->email = $r->email;
                $c->direccion = $r->direccion;
                $c->activo = 'S';
                if ($c->save()) {
                    return ['estado'=>'success','mensaje'=>'Cliente guardado!'];
                }else{
                    return ['estado'=>'failed','mensaje'=>'No fue posible guardar el cliente'];
                }
            }
        }else{
            return ['estado'=>'failed','mensaje'=>'El rut no es valido'];
        }


    }

    public function listar_clientes()
    {
        $cuerpo = Cliente::where('activo','S')->get();
        if (count($cuerpo)>0) {
            return[
                'estado'=>'success',
                'cuerpo'=>$cuerpo
            ];
        }
        return[
            'estado'=>'failed',
            'cuerpo'=>null
        ];
    }
    public function select_clientes()
    {
        $cuerpo = Cliente::select([
            'id',
            DB::raw("concat(nombres,' ',apellidos,' - ',rut) texto")
        ])
                         ->where('activo','S')->get();
        if (count($cuerpo)>0) {
            return[
                'estado'=>'success',
                'cuerpo'=>$cuerpo
            ];
        }
        return[
            'estado'=>'failed',
            'cuerpo'=>null
        ];
    }

    public function actualizar_cliente(Request $r)
    {
        
        $c = Cliente::find($r->cliente_id);
        
        if ($c) {
            if ($c->activo=='N') {
                return ['estado'=>'failed', 'mensaje'=>'No se pudo actualizar la información'];
            }
            $c->nombres = ucwords($r->nombres);
            $c->apellidos = ucwords($r->apellidos);
            $c->contacto = $r->contacto;
            $c->email = $r->email;
            $c->direccion = $r->direccion;
            if ($c->save()) {
                return ['estado'=>'success', 'mensaje'=>'Información del cliente actualizada'];
            }else{
                return ['estado'=>'failed', 'mensaje'=>'No se pudo actualizar la información'];
            }
        }

    }

    public function inhabilitar_cliente($id)
    {
        $c = Cliente::find($id);
        if ($c) {
            $c->activo='N';
            if ($c->save()) {
                return ['estado'=>'success','mensaje'=>'Cliente inhabilitado'];
            }
            return ['estado'=>'failed','mensaje'=>'Eror en la inhabilidad del cliente'];
        }
    }



    function validadorRut($trut)
    {
        $dvt = substr($trut, strlen($trut) - 1, strlen($trut));
        $rutt = substr($trut, 0, strlen($trut) - 1);
        $rut = (($rutt) + 0);
        $pa = $rut;
        $c = 2;
        $sum = 0;
        while ($rut > 0)
        {
            $a1 = $rut % 10;
            $rut = floor($rut / 10);
            $sum = $sum + ($a1 * $c);
            $c = $c + 1;
            if ($c == 8)
            {
                $c = 2;
            }
        }
        $di = $sum % 11;
        $digi = 11 - $di;
        $digi1 = ((string )($digi));
        if (($digi1 == '10'))
        {
            $digi1 = 'K';
        }
        if (($digi1 == '11'))
        {
            $digi1 = '0';
        }
        if (($dvt == $digi1))
        {
            return true;
        } else
        {
            return false;
        }
    }
}
