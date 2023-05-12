<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usr', function (Blueprint $table) {
            $table->char('id_usr', 64)->primary()->comment('Llave principal usuario');
            $table->string('usr_email', 64)->comment('Nombre usuario');
            $table->text('usr_passw')->comment('Password (encriptado)');
            $table->integer('usr_verificado');
            $table->date('usr_FechaAlta')->comment('Fecha de alta');
            $table->integer('usr_entradas')->nullable()->comment('Total de visitas a la página');
            $table->integer('id_tipo_usr')->index('XIF2usr')->comment('FK a tipo de usuatio');

            $table->unique(['id_usr', 'id_tipo_usr'], 'id_usr');
            $table->unique(['id_usr', 'id_tipo_usr'], 'XPKusr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usr');
    }
};
