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
        Schema::connection('dbclient')->create('tb_hemo', function (Blueprint $table) {
            $table->string('trx_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('reff_trx_id', 100)->nullable();
            $table->string('no_antrian', 100);
            $table->dateTime('tgl_daftar');
            $table->dateTime('tgl_kedatangan');
            $table->dateTime('tgl_pemeriksaan');
            $table->dateTime('tgl_selesai');
            $table->dateTime('tgl_diambil');
            $table->string('kunjungan_ke', 100);
            $table->text('catatan');
            $table->string('status', 100);

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            //$table->primary(['reg_id', 'trx_id', 'sub_trx_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_hemo');
    }
};
