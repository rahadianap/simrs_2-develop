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
        Schema::connection('dbclient')->create('tb_makanan_bahan', function (Blueprint $table) {
            $table->string('makanan_bahan_id', 100)->primary();
            $table->string('makanan_id', 100);
            $table->string('bahan_id', 100);
            $table->string('produk_id', 100);
            $table->string('produk_nama', 100);
            $table->double('jumlah', 18, 2);
            $table->string('satuan', 100);
            $table->string('kategori', 100);
            $table->tinyText('catatan')->nullable();
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 100);
            $table->string('updated_by', 100);
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
        Schema::dropIfExists('tb_makanan_bahan');
    }
};
