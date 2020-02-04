<?php

namespace App\Http\Controllers;

use App\Ventas;
use Illuminate\Http\Request;

class VentasController extends Controller
{
    public function registro_venta(Request $datos){
        return Ventas::registro_venta($datos);
     }

     public function traer_ventas(){
        return Ventas::traer_ventas();
    }

    public function buscar_venta_producto($producto){
        return Ventas::buscar_venta_producto($producto);
    }

    public function total_ventas(){
        return Ventas::total_ventas();
    }

}
