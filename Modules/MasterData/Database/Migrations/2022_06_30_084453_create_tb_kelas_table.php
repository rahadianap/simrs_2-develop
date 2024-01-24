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
        Schema::connection('dbclient')->create('tb_kelas', function (Blueprint $table) {
            $table->string('kelas_id', 100)->primary();
            $table->string('kelas_nama', 100);
            $table->string('inisial', 20)->nullable();
            $table->boolean('is_kelas_harga')->default(0);
            $table->boolean('is_kelas_kamar')->default(0);
            $table->string('kelas_bpjs', 100)->nullable();
            $table->string('rl_kode', 100)->nullable();
            $table->boolean('is_aktif')->default(0);
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
        Schema::connection('dbclient')->dropIfExists('tb_kelas');
    }
};
