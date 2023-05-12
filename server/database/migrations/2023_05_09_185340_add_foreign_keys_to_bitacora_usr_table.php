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
        Schema::table('bitacora_usr', function (Blueprint $table) {
            $table->foreign(['id_usr'], 'bitacora_usr_usr_id_usr_fk')->references(['id_usr'])->on('usr')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bitacora_usr', function (Blueprint $table) {
            $table->dropForeign('bitacora_usr_usr_id_usr_fk');
        });
    }
};
