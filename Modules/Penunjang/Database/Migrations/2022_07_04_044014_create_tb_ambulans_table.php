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
        Schema::connection('dbclient')->create('tb_ambulans', function (Blueprint $table) {
            $table->string('trx_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('reff_trx_id', 100)->nullable();
            $table->dateTime('tgl_permintaan');
            $table->dateTime('tgl_penggunaan')->nullable();
            $table->string('jenis_pemakaian', 100);
            $table->string('unit_asal_id', 100)->nullable();
            $table->string('ruang_asal_id', 100)->nullable();
            $table->string('nopol_ambulans', 100)->nullable();
            $table->string('pengemudi', 100)->nullable();
            $table->string('tenaga_medis', 100)->nullable();
            $table->boolean('is_meninggal')->nullable();
            $table->string('reff_pemulasaran', 100)->nullable();
            $table->string('nama_pengguna', 100)->nullable();
            $table->string('catatan')->nullable();
            $table->string('status', 50)->nullable();

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50);
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
        Schema::connection('dbclient')->dropIfExists('tb_ambulans');
    }
};
