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
        Schema::connection('dbclient')->create('tb_trx_operasi', function (Blueprint $table) {
            $table->string('trx_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('reff_trx_id', 100);
            
            $table->boolean('is_rujukan_int');
            $table->string('no_antrian', 8)->nullable();
            $table->string('cara_registrasi', 100)->nullable();
            $table->string('jns_registrasi', 100)->nullable();
            
            $table->dateTime('tgl_transaksi')->nullable();
            $table->date('tgl_operasi')->nullable();
            $table->time('jam_operasi')->nullable();
            $table->date('tgl_anestesi')->nullable();
            $table->time('jam_anestesi')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->time('jam_selesai')->nullable();
            //dokter operator dan pengirim
            $table->string('dokter_id', 100);
            $table->string('dokter_nama', 100);
            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $table->string('dokter_pengirim_id', 100)->nullable();
            $table->string('dokter_pengirim', 100)->nullable();
            //data unit dan ruang            
            $table->string('unit_id', 100);
            $table->string('unit_nama', 100);
            $table->string('ruang_id', 100);
            $table->string('ruang_nama', 100);
            $table->string('bed_id', 100)->nullable();
            $table->string('no_bed', 100)->nullable();
            
            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $table->string('unit_asal_id', 100)->nullable();
            $table->string('unit_asal', 100)->nullable();
            $table->string('ruang_asal_id', 100)->nullable();
            $table->string('ruang_asal', 100)->nullable();
            $table->string('bed_asal_id', 100)->nullable();
            $table->string('no_asal_bed', 100)->nullable();
            
            $table->string('asal_pasien', 100)->nullable();
            
            $table->string('penjamin_id', 100);
            $table->string('penjamin_nama', 100);
            $table->string('jns_penjamin', 50)->nullable();
            $table->boolean('is_bpjs')->default(0);

            $table->string('no_kepesertaan', 100)->nullable();
            $table->string('no_sep', 100)->nullable();
            $table->string('no_rujukan', 100)->nullable();
            
            $table->string('pasien_id', 100);
            $table->string('nama_pasien', 100);
            $table->string('no_rm', 100);
            $table->date('tgl_lahir')->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->string('nik', 30)->nullable();
            $table->string('jns_kelamin', 5)->nullable();
                        
            $table->string('buku_harga_id', 100);
            $table->string('buku_harga', 100)->nullable();
            $table->string('kelas_harga_id', 100);
            $table->string('kelas_harga', 100)->nullable();
            $table->string('kelas_penjamin_id', 100)->nullable();
            
            $table->tinyText('diagnosa_pra')->nullable();
            $table->tinyText('diagnosa_pasca')->nullable();
            $table->tinyText('catatan')->nullable();
            
            $table->string('tindakan_id', 100)->nullable();
            $table->string('tindakan_nama', 100)->nullable();
            $table->string('jenis_operasi', 30)->nullable();
            $table->string('skala_operasi', 20)->nullable();

            $table->string('status', 100)->nullable();
            $table->boolean('is_aktif');
            $table->boolean('is_realisasi')->default(0);
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
        Schema::connection('dbclient')->dropIfExists('tb_trx_operasi');
    }
};
