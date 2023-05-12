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
        Schema::create('usr_det', function (Blueprint $table) {
            $table->char('id_usr2', 64)->unique('id_usr2')->comment('Llave principal');
            $table->char('id_usr', 64)->index('XIF1usr2')->comment('FK a usuario');
            $table->text('usr2_nombre')->comment('Nombre (encriptado)');
            $table->text('usr2_apellido')->comment('Apellido (encriptado)');
            $table->text('usr2_cel1')->comment('Celular principal (encriptado)');
            $table->text('usr2_cel2')->nullable()->comment('Celular secundario (encriptado)');
            $table->text('usr2_mail2')->nullable()->comment('Correo secundario (encriptado)');
            $table->string('usr2_foto', 250)->nullable()->comment('Ruta de la foto');

            $table->unique(['id_usr2', 'id_usr'], 'id_usr2_2');
            $table->primary(['id_usr2']);
            $table->unique(['id_usr2', 'id_usr'], 'XPKusr2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usr_det');
    }
};
