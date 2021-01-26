<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class emisor_receptor_venta extends Model
{   //esta tabla en bd es para almacenar la informacion del receptor y cliente, ya sea de una factura o nota de credito
    //la factura va en tabla diferente al igual que la nota de credito
    protected $table="emisor_receptor_venta";

    //la referencia es en base a las notas de credito, si se esta haciendo una factura
    // deberian ir nulas, solo van con informacion cuando se hace una nota de credito
    protected function insertar($emisor, $receptor, $venta,$forma_pago, $referencia_id, $referencia){

        $in = $this;
        $in->venta_id = $venta->id;

        $in->emisor_razonsocial = $emisor['empresa'];
        $in->emi_rut = $emisor['rut'];
        $in->emi_direccion = $emisor['direccion'];
        // $in->emi_comuna = $emisor[''];
        // $in->emi_ciudad = $emisor;
        $in->emi_giro = $emisor['giro'];
        // $in->emi_email = $emisor;

        $in->res_razonsocial = $receptor['RazonSocial'];
        $in->res_rut = $receptor['Rut'];
        $in->res_direccion = $receptor['Direccion'];
        $in->res_comuna = $receptor['Comuna'];
        $in->res_ciudad = $receptor['Ciudad'];
        $in->res_giro = $receptor['Giro'];
        $in->res_email = $receptor['Email'];
        $in->res_contacto = $receptor['Contacto'];

        $in->emision = $venta->created_at;
        $in->cedible = ($forma_pago=='CREDITO')?'S':'N';
        $in->referencia_id = $referencia_id;
        $in->referencia = $referencia;

        if($in->save()){
            return [
                'estado' => 'success',
                'data' => $in
            ];
        }
        return [
            'estado' => 'failed',
            'data' => []
        ];
    }
}
