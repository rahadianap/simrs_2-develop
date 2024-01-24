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
        Schema::connection('dbclient')->create('tb_trx_pa', function (Blueprint $table) {
            $table->string('trx_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('reff_trx_id', 100)->nullable();
            $table->dateTime('tgl_permintaan');
            $table->dateTime('tgl_dibutuhkan')->nullable();
            $table->date('tgl_periksa')->nullable();
            $table->time('jam_periksa')->nullable();
            $table->dateTime('tgl_selesai')->nullable();
            $table->dateTime('tgl_diserahkan')->nullable();
            $table->boolean('is_cito');
            $table->string('jenis_cito', 100);
            $table->string('media_hasil', 50);
            $table->string('lokasi_jaringan', 100);
            $table->string('cara_pengambilan', 100);
            $table->string('diag_klinis', 100);
            $table->string('no_spesimen', 100);
            
            $table->string('dokter_id', 100);
            $table->string('dokter_nama', 100);
            $table->string('unit_id', 100);
            $table->string('unit_nama', 100);

            $table->string('pasien_id', 100);
            $table->string('nama_pasien', 100);

            $table->string('penjamin_id', 100);
            $table->string('penjamin_nama', 100);
            $table->string('jns_penjamin', 50)->nullable();
            
            $table->string('buku_harga_id', 100);
            $table->string('buku_harga', 100)->nullable();
            $table->string('kelas_harga_id', 100);
            $table->string('kelas_harga', 100)->nullable();
            $table->string('kelas_penjamin_id', 100)->nullable();

            
            $table->dateTime('tgl_haid_terakhir')->nullable();
            $table->string('diserahkan_oleh', 100)->nullable();
            $table->string('hub_penerima', 100)->nullable();
            $table->string('penerima_hasil', 100)->nullable();

            $table->boolean('is_realisasi')->default(0);
            $table->boolean('is_bayar')->nullable();

            $table->string('status', 50);

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
        Schema::connection('dbclient')->dropIfExists('tb_patologi_anatomi');
        Schema::connection('dbclient')->dropIfExists('tb_trx_pa');
    }
};
