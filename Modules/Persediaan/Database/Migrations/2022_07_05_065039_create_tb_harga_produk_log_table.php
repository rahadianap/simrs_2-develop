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
        Schema::connection('dbclient')->create('tb_harga_produk_log', function (Blueprint $table) {
            $table->string('harga_log_id')->primary();
            $table->string('reg_id', 100);
            $table->string('trx_id', 100);
            $table->string('detail_id', 100);
            $table->string('produk_id', 100);
            $table->dateTime('tgl_transaksi');
            $table->string('jns_transaksi', 50);
            $table->double('hna_lama', 18, 2);
            $table->double('hna_baru', 18, 2);
            $table->text('keterangan');
            $table->string('status', 50);

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
        Schema::connection('dbclient')->dropIfExists('tb_harga_produk_log');
    }
};
