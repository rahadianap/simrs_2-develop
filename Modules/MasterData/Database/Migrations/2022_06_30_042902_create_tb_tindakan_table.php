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
        Schema::connection('dbclient')->create('tb_tindakan', function (Blueprint $table) {
            $table->string('tindakan_id', 100)->primary();
            $table->string('tindakan_nama', 100);
            $table->string('deskripsi', 100)->nullable();
            $table->string('klasifikasi', 100)->nullable();
            $table->string('kelompok_tindakan_id', 100)->nullable();
            $table->string('kelompok_tagihan_id', 100)->nullable();
            $table->string('kelompok_vklaim_id', 100)->nullable();
            $table->text('meta_data')->nullable();
            $table->string('satuan', 50)->nullable();
            $table->boolean('is_paket')->default(0);
            $table->boolean('is_diskon')->default(0);
            $table->boolean('is_cito')->default(0);
            $table->boolean('is_hitung_admin')->default(0);
            $table->boolean('is_tampilkan_dokter')->default(0);
            $table->boolean('is_kamar')->default(0);
            $table->boolean('is_lab_hasil')->default(0);
            $table->string('rl_kode', 50)->nullable();
            $table->string('modality_id', 100)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_tindakan');
    }
};
