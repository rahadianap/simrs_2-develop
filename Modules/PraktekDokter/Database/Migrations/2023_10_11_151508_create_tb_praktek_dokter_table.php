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
        Schema::connection('dbclient')->create('tb_praktek_dokter', function (Blueprint $table) {
            $table->string('reg_id', 100)->primary();
            $table->dateTime('tgl_registrasi');
            $table->dateTime('tgl_periksa')->nullable();
            
            $table->string('pasien_id', 100);
            $table->string('nama_pasien', 100)->nullable();
            $table->string('no_rm', 20)->nullable();
            $table->string('jns_kelamin', 2)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->tinyText('alamat')->nullable();
            $table->boolean('is_pasien_baru')->default(0);
            
            $table->string('dokter_id', 100)->nullable();
            $table->string('dokter_nama', 100)->nullable();
            
            $table->string('penjamin_id', 100)->nullable();
            $table->string('penjamin_nama', 100)->nullable();

            $table->tinyText('catatan')->nullable();
            
            $table->string('status', 30);
            $table->boolean('is_periksa')->default(0);
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
        Schema::connection('dbclient')->dropIfExists('tb_pelayanan');
    }
};
