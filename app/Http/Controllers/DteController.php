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

        $new['Llave'] = 'test key';
        $new['Fecha'] = 'fecha test';

        if($r->sii_forma_pago == 'CONTADO'){
            $new['FormaPago'] = 0;

        }
        if($r->sii_forma_pago == 'CREDITO'){
            $new['FormaPago'] = 1;

        }
        $new['FormaPago_str'] = $r->sii_forma_pago;

        $new['emisor'] = $emisor['emisor'];
        $new['emisor']['empresa'] = strtoupper($emisor[0]['empresa']);
        $new['emisor']['giro'] = strtoupper($emisor[0]['giro']);
        $new['emisor']['rut'] = strtoupper($emisor[0]['rut']);
        $new['emisor']['dirreccion'] = strtoupper($emisor[0]['dirreccion']);

        $new['Cliente']['Ciudad'] = strtoupper($r->reseptor['ciudad']);
        $new['Cliente']['RazonSocial'] = strtoupper($r->reseptor['cliente']);
        $new['Cliente']['Comuna'] = strtoupper($r->reseptor['comuna']);
        $new['Cliente']['Contacto'] = strtoupper($r->reseptor['contacto']);
        $new['Cliente']['Direccion'] = strtoupper($r->reseptor['direccion']);
        $new['Cliente']['Email'] = strtoupper($r->reseptor['email']);
        $new['Cliente']['Giro'] = strtoupper($r->reseptor['giro']);
        $new['Cliente']['Rut'] = strtoupper($r->reseptor['rut']);

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
