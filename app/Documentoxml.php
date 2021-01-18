<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documentoxml extends Model
{
    protected $table = 'documento_xml';

    protected function insertar($venta, $r){

        $d = $this;
        $d->venta_id = $venta;
        $d->folio = $r['folio'];
        $d->xml = $r['dte'];
        $d->ted = $r['ted'];
        $d->activo = 'S';
        if($d->save()){
            return true;
        }
        return false;
    }
}
