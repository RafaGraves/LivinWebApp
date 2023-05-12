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
        Schema::create('tipo_usr', function (Blueprint $table) {
            $table->char('id_tipo_usr', 64)->primary()->comment('Clave principal tipo usuario');
            $table->string('tipo_usr_nombre', 18)->comment('Nombre del tipo');
            $table->string('tipo_usr_nota', 250)->nullable()->comment('Comentarios');

            $table->unique(['id_tipo_usr'], 'XPKstatus2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_usr');
    }
};
