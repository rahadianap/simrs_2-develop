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
        Schema::connection('dbclient')->create('tb_buku_harga', function (Blueprint $table) {
            $table->string('buku_harga_id', 100)->primary();
            $table->string('buku_nama', 100);
            $table->tinyText('deskripsi', 100);
            $table->boolean('is_standar_sistem')->default(0);
            $table->date('tgl_berlaku');
            $table->time('jam_berlaku');
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
        Schema::connection('dbclient')->dropIfExists('tb_buku_harga');
    }
};
