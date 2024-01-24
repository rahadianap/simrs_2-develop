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
        Schema::connection('dbclient')->create('tb_darah_detail', function (Blueprint $table) {
            $table->string('darah_detail_id', 100)->primary();
            $table->string('darah_terima_id', 100);
            $table->date('tgl_terima');            
            $table->string('darah_dist_id', 100)->nullable();
            $table->date('tgl_distribusi')->nullable();
            $table->time('jam_distribusi')->nullable();
            
            $table->string('darah_musnah_id', 100)->nullable();
            $table->date('tgl_pemusnahan')->nullable();
            $table->time('jam_pemusnahan')->nullable();

            $table->string('no_labu', 50);
            $table->string('gol_darah', 2);
            $table->string('rhesus', 15);
            $table->string('group_darah', 100);
            $table->integer('volume');
            $table->string('satuan', 20)->nullable();
            $table->integer('jumlah')->default(0);
            $table->date('tgl_kadaluarsa')->nullable();                   
            $table->tinyText('catatan')->nullable();
            $table->string('status', 20)->nullable();          
            $table->boolean('is_terpakai')->default(0);
            $table->boolean('is_musnah')->default(0);
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
        Schema::dropIfExists('tb_darah_detail');
    }
};
