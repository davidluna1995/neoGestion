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
        // $rut_limpio = $this->limpiar($r->rut);

        if($this->valida_rut($r->rut)){
            // $rut = (string)$rut_limpio;
            $verify = Cliente::whereRaw("upper(rut) = upper('$r->rut')")->first();
            if($verify){
                return ['estado'=>'failed','mensaje'=>'El rut ya estan en uso'];
            }else{
                if($r->tipo_cliente == 'PERSONA'){ // tipo persona
                    $c = new Cliente;
                    $c->rut = strtolower($r->rut);
                    $c->nombres = $nombres;
                    $c->apellidos = $apellidos;
                    $c->contacto = $r->contacto;
                    $c->email = $r->email;
                    $c->direccion = $r->direccion;
                    $c->comuna = $r->comuna;
                    $c->ciudad = $r->ciudad;
                    $c->giro = $r->giro;
                    $c->tipo_cliente = $r->tipo_cliente;
                    $c->activo = 'S';
                    if ($c->save()) {
                        return ['estado'=>'success','mensaje'=>'Cliente guardado!'];
                    }else{
                        return ['estado'=>'failed','mensaje'=>'No fue posible guardar el cliente'];
                    }
                }
                if($r->tipo_cliente == 'EMPRESA'){ //TIPO EMPRESA
                    $c = new Cliente;
                    $c->rut = strtolower($r->rut);
                    // $c->nombres = $nombres;
                    // $c->apellidos = $apellidos;
                    $c->razon_social = $r->razon_social;
                    $c->contacto = $r->contacto;
                    $c->email = $r->email;
                    $c->direccion = $r->direccion;
                    $c->comuna = $r->comuna;
                    $c->ciudad = $r->ciudad;
                    $c->giro = $r->giro;
                    $c->tipo_cliente = $r->tipo_cliente;
                    $c->activo = 'S';
                    if ($c->save()) {
                        return ['estado'=>'success','mensaje'=>'Empresa guardada!'];
                    }else{
                        return ['estado'=>'failed','mensaje'=>'No fue posible guardar el cliente'];
                    }
                }

            }
        }else{
            return ['estado'=>'failed','mensaje'=>'El rut no es valido'];
        }


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

    public function listar_clientes()
    {
        // $cuerpo = Cliente::where('activo','S')->get();
        $cuerpo = DB::select("SELECT
                            id,
                            rut,
                            tipo_cliente,
                            case
                                when tipo_cliente = 'PERSONA' then concat(nombres,' ',apellidos)
                                when tipo_cliente = 'EMPRESA' then razon_social
                            end as cliente,
                            nombres,
                            apellidos,
                            contacto,
                            email,
                            direccion,
                            comuna,
                            ciudad,
                            giro
                        from cliente WHERE activo = 'S' ORDER BY tipo_cliente ASC");
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

    public function cliente_deuda(){

        $listar = DB::select("SELECT
        case
            when c.tipo_cliente ='PERSONA' then concat(c.nombres,' ', c.apellidos)
            when c.tipo_cliente = 'EMPRESA' then razon_social
        end as cliente,
        c.contacto,
        v.id venta_id,
        to_char(v.created_at, 'DD/MM/YYYY HH24:MI') fecha,
        v.pago_credito monto_credito,
        v.detalle_credito,
		case
			when pc.pago = 'S' then '✔'
			when pc.pago is null then 'x'
			when pc.pago = 'N' then 'x'
			end as pago,
			pc.monto_pago,
			pc.descripcion,
			to_char(pc.created_at, 'DD/MM/YYYY HH24:MI') fecha_pago
        from ventas v
        inner join cliente c on c.id = v.cliente_id
		left join pago_credito pc on pc.venta_id = v.id
        where v.detalle_credito is not null or v.pago_credito is not null order by v.id desc");

        if(count($listar) > 0){
            return [ 'estado' => 'success', 'listar' => $listar ];
        }
        return [ 'estado' => 'failed', 'listar' => null ];


    }

    public function listar_cliente_rut($rut){

        $cuerpo = DB::select("SELECT
                            id,
                            rut,
                            tipo_cliente,
                            case
                                when tipo_cliente = 'PERSONA' then concat(nombres,' ',apellidos)
                                when tipo_cliente = 'EMPRESA' then razon_social
                            end as cliente,
                            contacto,
                            email,
                            direccion,
                            comuna,
                            ciudad,
                            giro
                        from cliente WHERE activo = 'S' and rut='$rut' ORDER BY tipo_cliente ASC");


        if (count($cuerpo)>0) {
            return[
                'estado'=>'success',
                'cuerpo'=>$cuerpo[0]
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
            if($r->tipo_cliente=='EMPRESA'){
                $c->razon_social = ucwords($r->razon_social);
            }else{
                $c->nombres = ucwords($r->nombres);
                $c->apellidos = ucwords($r->apellidos);
            }

            $c->contacto = ($r->contacto=='null')?null:$r->contacto;
            $c->email = ($r->email=='null')?null:$r->email;
            $c->direccion = ($r->direccion=='null')?null:$r->direccion;
            $c->comuna = ($r->comuna=='null')?null:$r->comuna;
            $c->ciudad = ($r->ciudad=='null')?null:$r->ciudad;
            $c->giro = ($r->giro=='null')?null:$r->giro;
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

    function limpiar($s)
    {
        $s = str_replace('á', 'a', $s);
        $s = str_replace('Á', 'A', $s);
        $s = str_replace('é', 'e', $s);
        $s = str_replace('É', 'E', $s);
        $s = str_replace('í', 'i', $s);
        $s = str_replace('Í', 'I', $s);
        $s = str_replace('ó', 'o', $s);
        $s = str_replace('Ó', 'O', $s);
        $s = str_replace('Ú', 'U', $s);
        $s= str_replace('ú', 'u', $s);

        //Quitando Caracteres Especiales
        $s= str_replace('"', '', $s);
        $s= str_replace(':', '', $s);
        $s= str_replace('.', '', $s);
        $s= str_replace(',', '', $s);
        $s= str_replace(';', '', $s);
        $s= str_replace('-', '', $s);
        return $s;
    }
}
