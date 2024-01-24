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
        Schema::connection('dbclient')->create('tb_pasien', function (Blueprint $table) {
            $table->string('pasien_id', 100);
            $table->string('no_rm', 20);
            $table->boolean('is_pasien_luar')->default(0);
            $table->string('nama_pasien', 100);
            $table->string('salut', 6);
            $table->string('nama_lengkap', 100);
            $table->string('nik', 24);
            $table->string('no_kk', 24)->nullable();
            $table->string('jns_kelamin', 15);
            $table->date('tgl_lahir');
            $table->string('tempat_lahir', 100);
            $table->string('jns_penjamin', 100);
            $table->string('penjamin_id', 100);
            $table->string('penjamin_nama', 100)->nullable();
            $table->string('no_kepesertaan', 50)->nullable();
            $table->dateTime('tgl_terakhir_periksa')->nullable();
            $table->boolean('is_hamil')->default(0);
            $table->boolean('is_meninggal')->default(0);
            $table->dateTime('tgl_meninggal')->nullable();
            $table->string('penyebab_kematian')->nullable();
            $table->dateTime('tgl_daftar')->nullable();
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['pasien_id','client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_pasien');
    }
};
