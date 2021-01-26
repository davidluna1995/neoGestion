<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmisorReceptorVentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emisor_receptor_venta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('venta_id');
            //emisor
            $table->text('emisor_razonsocial');
            $table->text('emi_rut');
            $table->text('emi_direccion');
            $table->text('emi_comuna')->nullable();
            $table->text('emi_ciudad')->nullable();
            $table->text('emi_giro');
            $table->text('emi_contacto')->nullable();
            $table->text('emi_email')->nullable();
            //reseptor
            $table->text('res_razonsocial');
            $table->text('res_rut');
            $table->text('res_direccion');
            $table->text('res_comuna');
            $table->text('res_ciudad');
            $table->text('res_giro');
            $table->text('res_contacto')->nullable();
            $table->text('res_email')->nullable();

            $table->datetime('emision')->nullable();
            $table->char('cedible', 1)->nullable();
            $table->integer('referencia_id')->nullable();
            $table->text('referencia')->nullable();
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
        Schema::dropIfExists('emisor_receptor_venta');
    }
}
