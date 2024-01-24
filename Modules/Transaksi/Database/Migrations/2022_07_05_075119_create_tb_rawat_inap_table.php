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
        Schema::connection('dbclient')->create('tb_rawat_inap', function (Blueprint $table) {
            $table->string('trx_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('reff_trx_id', 100);
            
            $table->dateTime('tgl_transaksi');
            $table->string('dokter_id', 100);
            $table->string('dokter_nama', 100);

            $table->string('dokter_pengirim_id', 100)->nullable();
            $table->string('dokter_pengirim', 100)->nullable();
            
            $table->string('unit_id', 100);
            $table->string('unit_nama', 100)->nullable();
            $table->string('ruang_id', 100);
            $table->string('ruang_nama', 100)->nullable();
            $table->string('bed_id', 100);
            $table->string('no_bed', 5);
            
            $table->date('tgl_masuk');
            $table->time('jam_masuk');            
            $table->date('tgl_pulang')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->string('status_pulang', 50)->nullable();
            $table->string('cara_pulang', 50)->nullable();
            $table->tinyText('catatan_pulang', 50)->nullable();
            
            $table->string('penjamin_id', 100);
            $table->string('penjamin_nama', 100)->nullable();
            $table->string('jns_penjamin', 30)->nullable();
            $table->string('no_kepesertaan', 30)->nullable();
            $table->string('no_sep',100)->nullable();
            $table->integer('plafon')->nullable();
            $table->boolean('is_bpjs')->default(0);
            
            $table->string('kelas_penjamin_id', 100)->nullable();
            $table->string('kelas_harga_id', 100);
            $table->string('kelas_harga', 100);
            $table->string('buku_harga_id', 100);
            $table->string('buku_harga', 100);

            $table->string('hub_penanggung', 100)->nullable();
            $table->string('nama_penanggung', 100)->nullable();
            
            $table->string('asal_pasien', 100)->nullable();
            $table->string('ket_asal_pasien', 100)->nullable();
            $table->string('cara_registrasi',100)->nullable();
            $table->string('jns_registrasi',100)->nullable();
            $table->boolean('is_konfirmasi')->default(0);            
            
            $table->string('pasien_id', 100);
            $table->string('no_rm', 20);
            $table->string('nama_pasien', 100);
            $table->smallInteger('usia')->nullable();
            $table->string('jns_kelamin', 15);
            $table->string('diag_awal', 100)->nullable();
            $table->string('status',30);
            $table->boolean('is_aktif');
            $table->boolean('is_pulang');
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
        Schema::connection('dbclient')->dropIfExists('tb_rawat_inap');
    }
};
