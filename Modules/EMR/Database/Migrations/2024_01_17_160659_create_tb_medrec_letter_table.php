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
        Schema::connection('dbclient')->create('tb_medrec_letter', function (Blueprint $table) {
            $table->string('letter_id', 100)->primary();
            $table->string('trx_id', 100);
            $table->string('reg_id', 100);
            $table->string('letter_tipe', 100);
            $table->datetime('tgl_keluar');
            
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_akhir')->nullable();
            $table->string('dokter_id', 100)->nullable();
            $table->string('dokter_nama', 100)->nullable();
            
            $table->string('pasien_id', 100)->nullable();
            $table->string('nama_pasien', 100)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('umur', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->text('catatan')->nullable();
            $table->text('keperluan')->nullable();
            $table->jsonb('pemeriksaan')->nullable();
            $table->string('hasil', 100)->nullable();

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
        Schema::connection('dbclient')->dropIfExists('tb_medrec_letter');
    }
};
