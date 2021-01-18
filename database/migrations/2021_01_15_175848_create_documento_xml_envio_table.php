<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentoXmlEnvioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_xml_envio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('venta_id')->nullable();
            $table->bigInteger('folio')->nullable();
            $table->text('xml_envio');
            $table->char('activo', 1);
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
        Schema::dropIfExists('documento_xml_envio');
    }
}
