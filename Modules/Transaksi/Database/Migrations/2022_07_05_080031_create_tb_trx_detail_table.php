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
        Schema::connection('dbclient')->create('tb_trx_detail', function (Blueprint $table) {
            $table->string('trx_id', 100);
            $table->string('detail_id', 100);
            $table->string('reg_id', 100);
            $table->string('jns_transaksi', 100);
            $table->string('kelas_harga_id', 100);
            $table->string('buku_harga_id', 100);
            $table->string('penjamin_id', 100);
            $table->string('item_id', 100);
            $table->string('item_nama', 100);
            $table->smallInteger('jumlah');
            $table->string('satuan', 100);
            $table->double('nilai', 18, 2);
            $table->double('diskon_persen', 18, 2);
            $table->double('diskon', 18, 2);
            $table->double('harga_diskon', 18, 2);
            $table->double('subtotal', 18, 2);
            $table->string('dokter_id', 100);
            $table->string('dokter_pengirim_id', 100);
            $table->boolean('is_hitung_adm');
            $table->string('group_tagihan', 100)->nullable();
            $table->string('group_eklaim', 100)->nullable();
            $table->string('rl_code', 100)->nullable();

            $table->boolean('is_realisasi')->default(0);
            $table->boolean('is_bayar')->default(0);
            $table->boolean('is_aktif');
            
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary([ 'trx_id', 'detail_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_trx_detail');
    }
};
