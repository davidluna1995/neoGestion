<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documentoxmlenvio extends Model
{
    protected $table ='documento_xml_envio';

    protected function insertar($venta, $r){

        $d = $this;
        $d->venta_id = $venta;
        $d->folio = $r['folio'];
        $d->xml_envio = $r['envio'];
        $d->activo = 'S';
        if($d->save()){
            return true;
        }
        return false;

    }
}
