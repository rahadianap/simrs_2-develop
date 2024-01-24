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
        Schema::connection('dbclient')->create('tb_persalinan_bayi', function (Blueprint $table) {
            $table->string('persalinan_bayi_id', 100);
            $table->string('persalinan_id', 100);
            $table->string('reg_id', 100);
            $table->string('trx_id', 100);
            $table->string('sub_trx_id', 100);
            
            $table->string('no_rm_bayi', 100);
            $table->date('tgl_kelahiran');
            $table->time('jam_kelahiran');
            $table->float('umur_kehamilan');
            $table->string('jenis_persalinan', 100);
            $table->string('kelahiran_ke', 100);
            $table->string('kondisi_ibu', 100);

            $table->string('jk_bayi', 100);
            $table->float('bb_bayi');
            $table->float('tb_bayi');
            $table->float('lingkar_kepala');
            $table->float('lingkar_dada');
            $table->float('frekuensi_napas');
            $table->float('detak_jantung');
            $table->string('kondisi_lahir', 100);
            
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['persalinan_bayi_id', 'persalinan_id', 'reg_id', 'trx_id', 'sub_trx_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_persalinan_bayi');
    }
};
