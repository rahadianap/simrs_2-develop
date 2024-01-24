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
        Schema::connection('dbclient')->table('tb_praktek_dokter', function (Blueprint $table) {
            // $table->double('total_tagihan', 18,2)->nullable();
            // $table->double('diskon', 18,2)->nullable();
            // $table->double('total_akhir', 18,2)->nullable();
            // $table->double('jml_bayar', 18,2)->nullable();
            // $table->double('kembalian', 18,2)->nullable();
            // $table->string('metode_pembayaran', 50)->nullable();
            $table->string('alasan_batal')->nullable();
            $table->string('interim_id')->nullable();
            $table->boolean('is_bayar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->table('tb_praktek_dokter', function (Blueprint $table) {

        });
    }
};
