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
        Schema::connection('dbclient')->create('tb_pemulasaran', function (Blueprint $table) {
            $table->string('trx_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('reff_trx_id', 100)->nullable();
            $table->dateTime('tgl_permintaan');
            $table->dateTime('tgl_jenazah_diterima')->nullable();
            $table->dateTime('tgl_kematian')->nullable();
            $table->string('sebab_kematian', 100)->nullable();
            $table->string('asal_unit', 100)->nullable();
            $table->string('asal_ruang', 100)->nullable();
            $table->dateTime('tgl_diserahkan')->nullable();
            $table->string('diserahkan_oleh', 100)->nullable();
            $table->string('diterima_oleh', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->string('dokter_id', 100)->nullable();
            $table->string('pasien_id', 100)->nullable();
            $table->string('nama_pasien', 100)->nullable();
            $table->smallInteger('usia_meniggal')->nullable();
            $table->string('status', 50)->nullable();

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            //$table->primary(['reg_id', 'trx_id', 'reff_trx_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_pemulasaran');
    }
};
