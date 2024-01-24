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
        Schema::connection('dbclient')->table('tb_resep_detail', function (Blueprint $table) {
            $table->renameColumn('klasifikasi', 'jenis_produk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->table('tb_resep_detail', function (Blueprint $table) {
            $table->renameColumn('jenis_produk', 'klasifikasi');
        });
    }
};
