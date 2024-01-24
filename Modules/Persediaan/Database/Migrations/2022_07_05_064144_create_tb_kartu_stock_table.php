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
        Schema::connection('dbclient')->create('tb_kartu_stock', function (Blueprint $table) {
            $table->string('stock_log_id')->primary();
            $table->string('reg_id', 100);
            $table->string('trx_id', 100);
            $table->string('sub_trx_id', 100);
            $table->string('detail_id', 100);
            $table->string('produk_id', 100);
            $table->dateTime('tgl_log');
            $table->dateTime('tgl_transaksi');
            $table->string('jns_transaksi', 50);
            $table->string('aksi', 50);
            $table->string('jns_produk', 50);
            $table->string('produk_nama', 100);
            $table->string('unit_id', 50);
            $table->string('depo_id', 100);
            $table->smallInteger('jml_awal');
            $table->smallInteger('jml_masuk');
            $table->smallInteger('jml_keluar');
            $table->smallInteger('jml_penyesuaian');
            $table->smallInteger('jml_akhir');
            $table->text('catatan');
            $table->text('keterangan');

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
        Schema::connection('dbclient')->dropIfExists('tb_kartu_stock');
    }
};
