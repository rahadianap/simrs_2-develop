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
        Schema::connection('dbclient')->create('tb_persalinan', function (Blueprint $table) {
            $table->string('persalinan_id', 100);
            $table->string('reg_id', 100);
            $table->string('trx_id', 100);
            $table->string('sub_trx_id', 100);
            
            $table->string('pasien_id', 100);
            $table->string('no_rm', 100);
            $table->string('nama_pasien', 100);
            $table->string('nik_pasien', 100);

            $table->string('nama_ayah_bayi', 100);
            $table->string('nik_ayah_bayi', 100);
            $table->string('alamat');

            $table->date('tgl_kelahiran');
            $table->time('jam_kelahiran');
            $table->float('umur_kehamilan');
            $table->string('jenis_persalinan', 100);
            $table->string('kelahiran_ke', 100);
            $table->string('kondisi_ibu', 100);
            
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['persalinan_id', 'reg_id', 'trx_id', 'sub_trx_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_persalinan');
    }
};
