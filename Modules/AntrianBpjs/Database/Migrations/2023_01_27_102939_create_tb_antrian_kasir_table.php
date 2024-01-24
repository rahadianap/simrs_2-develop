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
        Schema::connection('dbclient')->create('tb_antrian_kasir', function (Blueprint $table) {
            $table->string('antrian_kasir_id', 100)->primary();            
            $table->string('client_id', 100);
            $table->string('jns_antrian', 20)->nullable();
            $table->dateTime('tgl_ambil');
            $table->date('tgl_antrian');
            $table->string('no_antrian', 6); //antrian poli
            $table->integer('angka_antrian');

            $table->string('reg_id', 100)->nullable();
            $table->string('antrian_id', 100)->nullable();

            $table->dateTime('tgl_dilayani')->nullable();            
            $table->boolean('is_dilayani')->default(0);
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
        Schema::connection('dbclient')->dropIfExists('tb_antrian_kasir');
    }
};
