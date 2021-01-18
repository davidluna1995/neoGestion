<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter001DetalleVentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detalle_venta', function (Blueprint $table) {
            $table->float('porcentaje_descuento',8, 2)->nullable();
            $table->string('afecto_iva')->nullable();
            $table->string('unidad')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalle_venta', function (Blueprint $table) {

        });
    }
}
