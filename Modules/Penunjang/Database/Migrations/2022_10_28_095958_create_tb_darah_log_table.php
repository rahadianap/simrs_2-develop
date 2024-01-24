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
        Schema::connection('dbclient')->create('tb_darah_log', function (Blueprint $table) {
            $table->string('log_darah_id', 100)->primary();
            $table->string('trx_darah_id', 100);
            $table->string('darah_detail_id', 100);
            $table->date('tgl_transaksi');            
            $table->string('jenis_transaksi', 100);
            
            $table->date('tgl_distribusi')->nullable();
            $table->time('jam_distribusi')->nullable();
            
            $table->string('darah_musnah_id', 100)->nullable();
            $table->date('tgl_pemusnahan')->nullable();
            $table->time('jam_pemusnahan')->nullable();

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
        Schema::connection('dbclient')->dropIfExists('tb_darah_log');
    }
};
