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
        Schema::connection('dbclient')->create('tb_pengadaan_harga', function (Blueprint $table) {
            $table->string('detail_id', 100);
            $table->string('pengadaan_id', 100);
            $table->string('trx_id', 100);
            $table->string('reff_trx_id', 100);
            $table->string('produk_id', 100);
            $table->string('produk_nama', 100);
            $table->string('satuan_id', 100);
            $table->string('satuan_beli_id', 100);
            $table->float('konversi');
            $table->smallInteger('jml_transaksi');
            $table->smallInteger('jml_total_transaksi');
            $table->double('harga', 18, 2);
            $table->string('tipe_pajak', 50);
            $table->smallInteger('persen_pajak');
            $table->double('harga_sblm_pajak', 18, 2);
            $table->double('nilai_pajak', 18, 2);
            $table->boolean('is_diskon_persen');
            $table->double('diskon', 18, 2);
            $table->double('harga_akhir', 18, 2);
            $table->double('subtotal', 18, 2);
            $table->double('hna', 18, 2);

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(array('detail_id', 'pengadaan_id', 'trx_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_pengadaan_harga');
    }
};
