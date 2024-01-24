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
        Schema::connection('dbclient')->create('tb_reg_pasien', function (Blueprint $table) {
            $table->string('reg_id', 100)->primary();
            $table->boolean('is_pasien_luar')->default(0);
            $table->string('pasien_id', 100);
            $table->string('no_rm', 100);
            $table->string('nama_pasien', 100);
            $table->string('tempat_lahir', 100);
            $table->date('tgl_lahir');
            $table->string('usia', 100)->nullable();
            $table->string('jns_kelamin', 15);
            $table->string('propinsi_id', 100)->nullable();
            $table->string('kota_id', 100)->nullable();
            $table->string('kecamatan_id', 100)->nullable();
            $table->string('kelurahan_id', 100)->nullable();
            $table->string('kodepos', 10)->nullable();
            $table->boolean('is_hamil')->nullable();
            $table->boolean('is_pasien_baru')->nullable();

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_reg_Pasien');
    }
};
