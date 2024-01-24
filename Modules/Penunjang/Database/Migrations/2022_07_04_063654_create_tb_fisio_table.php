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
        Schema::connection('dbclient')->create('tb_fisio', function (Blueprint $table) {
            $table->string('reg_id', 100);
            $table->string('trx_id', 100);
            $table->string('sub_trx_id', 100);
            $table->string('no_antrian', 100);
            $table->dateTime('tgl_permintaan');
            $table->dateTime('tgl_pemeriksaan')->nullable();
            $table->string('indikasi', 100)->nullable();
            $table->text('diagnosa')->nullable();
            $table->string('kunjungan_id', 100)->nullable();
            $table->integer('kunjungan_ke')->nullable();
            $table->text('catatan');
            $table->string('status', 100);

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
        Schema::connection('dbclient')->dropIfExists('tb_fisio');
    }
};
