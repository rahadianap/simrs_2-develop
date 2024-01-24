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
        Schema::connection('dbclient')->create('tb_pr_detail', function (Blueprint $table) {
            $table->string('pr_detail_id', 100)->primary();
            $table->string('pr_id', 100);
            $table->string('jenis_produk',20);
            $table->string('produk_id', 100);
            $table->string('produk_nama');
            $table->smallInteger('jml_satuan');
            $table->string('satuan', 100);
            $table->float('konversi')->nullable();
            $table->smallInteger('jml_pr');
            $table->string('pr_satuan', 100);
            $table->string('pengadaan_id', 100)->nullable();
            $table->string('trx_id', 100)->nullable();
            $table->string('status', 50)->nullable();
            
            $table->string('vendor_id', 100)->nullable();
            $table->string('vendor_nama', 100)->nullable();

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
        Schema::connection('dbclient')->dropIfExists('tb_pr_detail');
    }
};
