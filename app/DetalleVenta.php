<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleVenta extends Model
{
    use softDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'detalle_venta';
}
