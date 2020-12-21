<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //RECORDAR QUE ESTA TABLA HAY QUE DEJAR EN NULL NOMBRE Y APELLIDO
        Schema::table('cliente', function (Blueprint $table) {
            $table->text('razon_social')->nullable();
            $table->text('comuna')->nullable();
            $table->text('ciudad')->nullable();
            $table->text('giro')->nullable();
            $table->string('tipo_cliente')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cliente', function (Blueprint $table) {
            //
        });
    }
}
