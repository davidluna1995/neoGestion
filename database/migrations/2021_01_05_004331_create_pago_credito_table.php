<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagoCreditoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { //historial de pagos de credito
        Schema::create('pago_credito', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('venta_id');
            $table->char('pago', 1); // S, N
            $table->text('descripcion')->nullable(); // observacion del pago o algo asi
            $table->bigInteger('monto_pago');
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
        Schema::dropIfExists('pago_credito');
    }
}
