<?php

namespace App\Http\Controllers;

use App\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function registro_producto(Request $datos){
        return Producto::registro_producto($datos);
     }

     public function traer_productos(){
        return Producto::traer_productos();
    }

    public function modificar_campo_producto(Request $datos){
        return Producto::modificar_campo_producto($datos);
    }

    public function cantidad_productos(){
        return Producto::cantidad_productos();
    }

    public function buscar_producto($producto){
        return Producto::buscar_producto($producto);
    }


}
