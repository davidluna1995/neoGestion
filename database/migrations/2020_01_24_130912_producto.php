<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Producto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('categoria_id');
            $table->string('nombre');
            $table->string('descripcion');
            $table->bigInteger('cantidad');
            $table->bigInteger('precio_compra');
            $table->bigInteger('precio_venta');
            // $table->date('fecha');
            // $table->time('hora');
            // $table->char('activo',1);
            $table->softDeletes();
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
        Schema::dropIfExists('producto');
    }
}
