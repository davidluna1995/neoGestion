<?php

namespace App\Http\Controllers;

use App\Configuraciones;
use Illuminate\Http\Request;

class DteController extends Controller
{


    public function ver_antes_dte_33(Request $r){

        $emisor = Configuraciones::all();

        $r['emisor'] = $emisor[0];
        // $r = array_map('strtoupper', $r);


        return ['estado'=>'success', 'factura'=>$r->all()];


    }
}
