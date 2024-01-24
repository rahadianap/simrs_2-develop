<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbclient')->table('tb_asesmen', function (Blueprint $table) {
            $table->string('reg_id')->nullable();
            $table->string('reff_trx_id')->nullable();
            $table->string('dokter_id')->nullable();
            $table->string('dokter_nama')->nullable();
            $table->dateTime('tgl_assesmen')->nullable();
            $table->string('pasien_id')->nullable();
            $table->string('nama_pasien')->nullable();
            $table->string('no_rm', 20)->nullable();
            $table->string('unit_id')->nullable();
            $table->string('unit_nama')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->table('tb_asesmen', function (Blueprint $table) {

        });
    }
};
