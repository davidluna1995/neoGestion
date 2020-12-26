<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Configuraciones extends Model
{
    use softDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'configuraciones';


    public function setEmpresaAttribute($value)
    {
        $this->attributes['empresa'] = strtoupper($value);
    }
}
