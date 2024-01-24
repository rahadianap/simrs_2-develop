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
        Schema::connection('dbclient')->create('tb_operasi', function (Blueprint $table) {
            $table->string('reg_id', 100);
            $table->string('trx_id', 100);
            $table->string('sub_trx_id', 100);
            
            $table->string('pasien_id', 100);
            $table->string('no_rm', 100);
            $table->string('nama_pasien', 100);
            $table->string('unit_asal_id', 100)->nullable();
            $table->string('unit_asal', 100)->nullable();
            
            $table->string('jenis_tindakan', 100); //cito atau elektif
            $table->string('skala_operasi', 100)->nullable(); //khusus, kecil,besar, sedang 

            $table->dateTime('tgl_booking');
            $table->date('tgl_operasi');
            $table->time('jam_mulai');
            $table->date('tgl_selesai');
            $table->time('jam_selesai'); 
            $table->string('ruang_id',100);
            $table->string('ruang_nama',100);
                        
            $table->string('diag_pra_operasi',100);
            
            $table->string('tindakan_operasi_id', 100)->nullable(); // nama tindakan operasi
            $table->string('tindakan_operasi', 100)->nullable(); // nama tindakan operasi
            $table->string('jenis_anasthesi',100)->nullable();
            $table->date('tgl_anasthesi')->nullable();
            $table->time('jam_anasthesi')->nullable();
            
            $table->string('diag_pasca_operasi', 100)->nullable();
            $table->tinyText('catatan_operasi')->nullable();
            $table->tinyText('laporan_operasi')->nullable();
            
            $table->boolean('is_pa')->default(0);
            $table->string('pa_trx_id')->nullable();
            $table->string('jaringan_eksisi_insisi', 100)->nullable(); //catatan jaringan yang di eksisi
            $table->boolean('is_operasi_tambahan')->default(0);
            $table->string('status', 100); //booking->operasi->selesai

            //status operasi dan pasien
            $table->boolean('is_meninggal')->default(0);
            $table->string('ruang_recovery_id')->nullable();
            $table->string('ruang_recovery')->nullable();
            
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['reg_id', 'trx_id', 'sub_trx_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_operasi');
    }
};
