<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CajaUser extends Model
{
    protected $table = 'caja_user';



    protected function ver_usuarios_en_caja($caja_id){

        $tabla = DB::select("SELECT cu.id caja_user_id, name
                            from caja_user cu
                            inner join users u on u.id = cu.user_id where cu.activo = 'S'
                             AND cu.caja_id = $caja_id");

        if(count($tabla) > 0){
            return ['estado'=>'success', 'users'=> $tabla];
        }else{
            return ['estado'=>'failed', 'users'=> []];
        }

    }
}
