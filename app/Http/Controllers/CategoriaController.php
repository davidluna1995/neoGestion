<?php

namespace App\Http\Controllers;

use App\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
    public function registro_categoria(Request $datos){
        return Categoria::registro_categoria($datos);
     }

     public function traer_categorias(){
        return Categoria::traer_categorias();
    }

    public function modificar_campo_categoria(Request $datos){
        return Categoria::modificar_campo_categoria($datos);
    }

    public function buscar_categoria($categoria){
        return Categoria::buscar_categoria($categoria);
    }
    public function cantidad_categoria(){
        return Categoria::cantidad_categoria();
    }
}
