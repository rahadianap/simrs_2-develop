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
        Schema::connection('dbclient')->create('tb_asset_detail', function (Blueprint $table) {
            $table->string('detail_asset_id', 100)->primary();
            $table->string('asset_id', 100);
            $table->string('nama_asset', 100);
            $table->string('nomor_seri', 100);
            $table->string('lokasi', 100);
            $table->tinyText('deskripsi');
            $table->dateTime('tgl_perolehan');
            $table->dateTime('tgl_pemakaian');
            $table->float('nilai_asset');
            $table->float('masa_pakai', 100);
            $table->string('status', 30);

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
        Schema::dropIfExists('tb_asset_detail');
    }
};
