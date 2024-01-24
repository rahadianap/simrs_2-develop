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
        Schema::connection('dbclient')->create('tb_transaksi', function (Blueprint $table) {
            $table->string('trx_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('reff_trx_id', 100);

            $table->string('jns_transaksi', 100);
            $table->dateTime('tgl_transaksi');
            $table->string('no_antrian', 20)->nullable();
            $table->string('no_transaksi', 100)->nullable();
            
            $table->string('kelas_id', 100);
            $table->string('kelas_harga_id', 100);
            $table->string('kelas_penjamin_id', 100)->nullable();
            
            $table->string('penjamin_id', 100);
            $table->string('penjamin_nama', 100);
            $table->string('unit_id', 100);
            $table->string('unit_nama', 100);
            $table->string('ruang_id', 100);
            $table->string('ruang_nama', 100);
            $table->string('dokter_id', 100);
            $table->string('dokter_nama', 100);
            
            $table->string('dokter_pengirim_id', 100)->nullable();
            $table->string('dokter_pengirim', 100)->nullable();
            $table->string('unit_pengirim_id', 100)->nullable();
            $table->string('unit_pengirim', 100)->nullable();

            $table->string('pasien_id', 100);
            $table->string('no_rm', 20);
            $table->string('nama_pasien', 100);
            $table->string('status', 50);
            $table->boolean('is_realisasi')->default(0);
            $table->boolean('is_bayar')->default(0);
            $table->string('buku_harga_id', 100);
            $table->string('buku_harga', 100)->nullable();
            $table->double('total',18,2)->nullable();            

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_transaksi');
    }
};
