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
        Schema::connection('dbclient')->create('tb_depo_produk', function (Blueprint $table) {
            $table->string('depo_produk_id', 100)->primary();
            $table->string('depo_id', 100);
            $table->string('produk_id', 100);            
            $table->string('jenis_produk', 50);
            $table->smallInteger('jml_awal');
            $table->smallInteger('jml_masuk');
            $table->smallInteger('jml_keluar');
            $table->smallInteger('jml_penyesuaian');
            $table->smallInteger('jml_total');
            $table->smallInteger('total_so');
            $table->smallInteger('selisih_so');
            $table->boolean('is_aktif');
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
        Schema::connection('dbclient')->dropIfExists('tb_depo_produk');
    }
};
