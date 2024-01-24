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
        Schema::connection('dbclient')->create('tb_rawat_jalan', function (Blueprint $table) {
            $table->string('trx_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('reff_trx_id', 100);
            
            $table->string('jns_registrasi', 50)->nullable();
            $table->string('cara_registrasi', 50)->nullable();
            $table->string('asal_pasien', 100)->nullable();
            $table->string('ket_asal_pasien', 100)->nullable();
            
            $table->dateTime('tgl_transaksi');
            $table->date('tgl_periksa');
            $table->time('jam_periksa');            
            $table->string('no_antrian', 10);
            $table->string('dokter_id', 100);
            $table->string('dokter_nama', 100)->nullable();
            $table->string('dokter_pengirim_id', 100)->nullable();
            $table->string('dokter_pengirim', 100)->nullable();
            $table->string('unit_id', 100);
            $table->string('unit_nama', 100)->nullable();
            $table->string('ruang_id', 100);
            $table->string('ruang_nama', 100)->nullable();
            
            $table->string('jadwal_id', 100)->nullable();
            $table->dateTime('tgl_pulang')->nullable();
            $table->string('status_pulang', 50)->nullable();
            $table->string('cara_pulang', 50)->nullable();
            
            $table->string('penjamin_id', 100);
            $table->string('penjamin_nama', 100)->nullable();
            $table->string('jns_penjamin', 50)->nullable();
            
            $table->string('kelas_penjamin_id', 100);
            $table->string('kelas_harga_id', 100);
            $table->string('kelas_harga', 100)->nullable();
            $table->string('buku_harga_id', 100);
            $table->string('buku_harga', 100)->nullable();
                        
            $table->string('no_kepesertaan', 50)->nullable();
            $table->string('no_rujukan', 50)->nullable();
            $table->string('no_sep', 50)->nullable();
            $table->boolean('is_bpjs')->default(0);
            
            $table->string('pasien_id', 100);
            $table->string('no_rm', 20);
            $table->string('nama_pasien', 100);
            $table->smallInteger('usia')->nullable();
            $table->string('jns_kelamin', 15);
            $table->string('diag_awal', 100)->nullable();
            $table->date('haid_terakhir')->nullable();
            $table->boolean('is_hamil')->default(0);
            $table->boolean('is_kecelakaan')->default(0);
            $table->string('status',30);
            $table->boolean('is_konfirmasi')->default(0);
            $table->string('nama_penanggung', 100)->nullable();
            $table->string('hub_penanggung', 50)->nullable();
            
            $table->boolean('is_berkas_terima')->default(0);
            $table->dateTime('tgl_berkas_terima')->nullable();

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
        Schema::connection('dbclient')->dropIfExists('tb_rawat_jalan');
    }
};
