<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistroCajaVendedorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registro_caja_vendedor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha_inicio'); // cambiar a timestamp
            $table->date('fecha_cierre')->nullable(); // cambiar a timestamp
            $table->bigInteger('monto_inicio');
            $table->bigInteger('monto_cierre')->nullable();
            $table->char('activo', 1);
            $table->integer('caja_id');
            $table->integer('user_id');
            $table->bigInteger('periodo_caja_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registro_caja_vendedor');
    }
}
