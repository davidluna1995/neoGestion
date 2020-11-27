<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodoCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periodo_caja', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha_inicio'); // cambiar a timestamp
            $table->date('fecha_cierre')->nullable(); // cambiar a timestamp
            $table->bigInteger('monto_inicio'); // el primero que abra una caja el monto ira aqui, y se iran sumando todos los montos de las cajas que abran
            $table->bigInteger('monto_cierre')->nullable();
            $table->char('activo',1); // N, S
            $table->integer('inicio_user_id');
            $table->integer('cierre_user_id')->nullable();

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
        Schema::dropIfExists('periodo_caja');
    }
}
