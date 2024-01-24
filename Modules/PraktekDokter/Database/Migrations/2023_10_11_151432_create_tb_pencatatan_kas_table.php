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
        Schema::connection('dbclient')->create('tb_pencatatan_kas', function (Blueprint $table) {
            $table->string('kas_id', 100)->primary();
            $table->dateTime('tgl_transaksi');
            $table->string('jenis_transaksi', 100);
            $table->tinyText('deskripsi');
            $table->string('metode_pembayaran', 50);
            $table->double('jml_bayar', 18,2);
            $table->string('bukti_bayar')->nullable();
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50);
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
        Schema::connection('dbclient')->dropIfExists('tb_pencatatan_kas');
    }
};
