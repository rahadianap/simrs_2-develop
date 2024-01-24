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
        Schema::connection('dbclient')->create('tb_ruang', function (Blueprint $table) {
            $table->string('ruang_id', 100)->primary();
            $table->string('unit_id', 100);
            $table->string('ruang_nama', 50);
            $table->boolean('is_utama')->nullable(); //penanda untuk default ruang di unit terkait
            $table->string('kelas_harga', 100)->nullable();
            $table->string('kelas_kamar', 100)->nullable();
            $table->string('harga_id', 100)->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->string('deskripsi', 100)->nullable();

            $table->boolean('is_ruang_isolasi')->default(0);
            $table->boolean('is_ventilator')->default(0);
            $table->boolean('is_negative_pressure')->default(0);
            $table->boolean('is_ruang_operasi')->default(0);
            $table->boolean('is_pandemi')->default(0);
            
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
        Schema::connection('dbclient')->dropIfExists('tb_ruang');
    }
};
