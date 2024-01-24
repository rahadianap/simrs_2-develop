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
        Schema::connection('dbclient')->create('tb_komp_harga', function (Blueprint $table) {
            $table->string('komp_harga_id', 100)->primary();
            $table->string('komp_harga', 100);
            $table->string('deskripsi', 100)->nullable();
            $table->boolean('is_jasa_dokter')->default(0);
            $table->boolean('is_hitung_pajak')->default(0);
            $table->boolean('is_auto_hitung')->default(0);
            $table->string('jenis_pajak', 100)->nullable();

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
        Schema::connection('dbclient')->dropIfExists('tb_komp_harga');
    }
};
