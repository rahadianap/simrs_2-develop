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
        Schema::connection('dbclient')->create('tb_bank_darah', function (Blueprint $table) {
            $table->string('trx_id', 100)->primary();
            $table->string('reff_trx_id', 100)->nullable();
            $table->string('reg_id', 100);
            $table->dateTime('tgl_permintaan');
            $table->dateTime('tgl_dibutuhkan')->nullable();
            $table->dateTime('tgl_kirim')->nullable();
            $table->string('indikasi', 100)->nullable();
            $table->string('diagnosa', 100)->nullable();
            $table->string('dokter_peminta', 100)->nullable();
            $table->string('penerima', 100)->nullable();
            $table->string('pengirim', 100)->nullable();
            $table->string('status', 100)->nullable();

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            //$table->primary(['trx_id', 'sub_trx_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_bank_darah');
    }
};
