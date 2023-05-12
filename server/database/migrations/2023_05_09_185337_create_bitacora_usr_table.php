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
        Schema::create('bitacora_usr', function (Blueprint $table) {
            $table->integer('id_bitacora')->comment('id bitácora usr');
            $table->char('id_usr', 64)->index('bitacora_usr_usr_id_usr_fk')->comment('FK usuario');
            $table->dateTime('bit_entrada')->useCurrent()->comment('Fecha y hora de la entrada');
            $table->dateTime('bit_salida')->useCurrent()->comment('Fecha y hora de salida');

            $table->unique(['id_bitacora', 'id_usr'], 'XPKbitacora');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bitacora_usr');
    }
};
