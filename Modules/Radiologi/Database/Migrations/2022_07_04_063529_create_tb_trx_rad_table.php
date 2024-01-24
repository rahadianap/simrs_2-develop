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
        Schema::connection('dbclient')->create('tb_trx_rad', function (Blueprint $table) {
            $table->string('trx_id', 100)->primary();            
            $table->string('reg_id', 100);
            $table->string('reff_trx_id', 100);
            
            $table->dateTime('tgl_permintaan');
            $table->date('tgl_periksa')->nullable();
            $table->time('jam_periksa')->nullable();
            $table->datetime('tgl_hasil')->nullable();
            $table->datetime('tgl_diserahkan')->nullable();
            $table->boolean('is_rujukan_int')->default(0);
            $table->string('no_antrian', 6)->nullable();     
            //dokter rad
            $table->string('dokter_id', 100);
            $table->string('dokter_nama', 100);

            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $table->string('dokter_pengirim_id', 100)->nullable();
            $table->string('dokter_pengirim', 100)->nullable();
            
            //diisi dengan dokter ekspertise
            $table->string('expertise_id', 100)->nullable();
            $table->string('expertise_nama', 100)->nullable();
            
            //data unit lab            
            $table->string('unit_id', 100);
            $table->string('unit_nama', 100);
            $table->string('ruang_id', 100);
            $table->string('ruang_nama', 100);
            
            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $table->string('unit_asal_id', 100)->nullable();
            $table->string('unit_asal', 100)->nullable();
            $table->string('ruang_asal_id', 100)->nullable();
            $table->string('ruang_asal', 100)->nullable();
            $table->string('bed_asal_id', 100)->nullable();
            $table->string('no_bed_asal', 100)->nullable();
            
            $table->string('asal_pasien', 100)->nullable();
            
            $table->string('penjamin_id', 100);
            $table->string('penjamin_nama', 100);
            $table->string('jns_penjamin', 50)->nullable();
            $table->string('no_kepesertaan', 100)->nullable();
            
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

            $table->string('media_hasil', 100)->nullable();
            $table->string('diagnosa', 100)->nullable();
            $table->string('ket_klinis', 100)->nullable();
            $table->boolean('is_cito')->nullable();
            $table->string('jenis_cito', 100)->nullable();
            $table->boolean('is_multi_hasil')->nullable();
            $table->string('diserahkan_oleh', 100)->nullable();
            $table->string('penerima_hasil', 100)->nullable();
            $table->string('hub_penerima', 100)->nullable();
            $table->string('status', 100)->nullable();

            $table->boolean('is_realisasi')->default(0);
            $table->boolean('is_bayar')->nullable();
            $table->string('jns_registrasi', 100)->nullable();
            $table->string('cara_registrasi', 100)->nullable();

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
        Schema::connection('dbclient')->dropIfExists('tb_radiologi');
    }
};
