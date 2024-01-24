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
        Schema::connection('dbclient')->create('tb_jenis_produk_darah', function (Blueprint $table) {
            $table->string('jenis_produk_darah_id', 100)->primary();    
            $table->string('jenis_produk_darah', 100);           
            $table->string('inisial', 20);
            $table->tinyText('deskripsi')->nullable();
            $table->string('tindakan_id', 100);           
            $table->string('tindakan_nama', 100);
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
        Schema::connection('dbclient')->dropIfExists('tb_jenis_produk_darah');
    }
};
