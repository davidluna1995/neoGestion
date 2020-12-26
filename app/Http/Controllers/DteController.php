<?php

namespace App\Http\Controllers;

use App\Configuraciones;
use Illuminate\Http\Request;

class DteController extends Controller
{


    public function ver_antes_dte_33(Request $r){
        $new = [];
        $emisor = Configuraciones::all();

        $emisor['emisor'] = $emisor[0];
        $new['emisor'] = $emisor['emisor'];
        $new['emisor']['empresa'] = strtoupper($emisor[0]['empresa']);
        $new['emisor']['giro'] = strtoupper($emisor[0]['giro']);
        $new['emisor']['rut'] = strtoupper($emisor[0]['rut']);
        $new['emisor']['dirreccion'] = strtoupper($emisor[0]['dirreccion']);

        $new['reseptor']['ciudad'] = strtoupper($r->reseptor['ciudad']);
        $new['reseptor']['cliente'] = strtoupper($r->reseptor['cliente']);
        $new['reseptor']['comuna'] = strtoupper($r->reseptor['comuna']);
        $new['reseptor']['contacto'] = strtoupper($r->reseptor['contacto']);
        $new['reseptor']['direccion'] = strtoupper($r->reseptor['direccion']);
        $new['reseptor']['email'] = strtoupper($r->reseptor['email']);
        $new['reseptor']['giro'] = strtoupper($r->reseptor['giro']);
        $new['reseptor']['rut'] = strtoupper($r->reseptor['rut']);

        $i=0;
       foreach ($r->carro as $c) {

            $new['Productos'][$i]['NombreProducto'] = strtoupper($c['nombre']);
            $new['Productos'][$i]['DescripcionAdicional'] = strtoupper('...');
            $new['Productos'][$i]['Afecto'] = $c['afecto'];
            $new['Productos'][$i]['Cantidad'] = $c['cantidad_ls'];
            $new['Productos'][$i]['PrecioNeto'] = $c['precio'];
            $new['Productos'][$i]['DescuentoNeto'] = $c['descuento'];
            $new['Productos'][$i]['TipoImpAdicional'] =$c['tipo_impuesto_adicional'];
            $new['Productos'][$i]['MontoImpAdicional'] = $c['monto_impuesto_adicional'];
            $new['Productos'][$i]['UnidadMedida'] = $c['unidad'];
            $new['Productos'][$i]['SubTotal'] =$c['precio'];

            $i++;
       }



        // $r->reseptor->ciudad =strtoupper($r['reseptor']['ciudad']);
        // $r = array_map('strtoupper', $r);
        return ['estado'=>'success', 'factura'=>$new, 'factura_origin' => $r->all()];


        return ['estado'=>'success', 'factura'=>$r->all()];


    }
}
