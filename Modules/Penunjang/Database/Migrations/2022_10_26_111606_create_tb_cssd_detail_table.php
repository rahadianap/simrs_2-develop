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
        Schema::connection('dbclient')->create('tb_cssd_detail', function (Blueprint $table) {
            $table->string('cssd_detail_id', 100)->primary();
            $table->string('trx_cssd_id', 100);
            $table->string('trx_cssd_reff', 100)->nullable();
            $table->string('jenis_transaksi', 50);
            $table->date('tgl_transaksi');
            $table->time('jam_transaksi');
            
            $table->string('unit_id', 100);
            $table->string('unit_nama', 100)->nullable();
            
            $table->string('produk_id', 100);
            $table->string('produk_nama', 100)->nullable();
            $table->string('satuan', 20)->nullable();
            
            $table->integer('jml_terima')->default(0);
            $table->integer('jml_rusak')->default(0);
            $table->integer('jml_perawatan')->default(0);
            $table->integer('jml_keluar')->default(0);
            $table->integer('jml_penyesuaian')->default(0);
            $table->integer('jml_awal')->default(0);       
                 
            $table->string('kondisi', 30)->nullable();
            $table->string('status', 20)->nullable();
            
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
        Schema::connection('dbclient')->dropIfExists('tb_cssd_detail');
    }
};
