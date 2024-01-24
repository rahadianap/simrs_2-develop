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
        Schema::connection('dbclient')->create('tb_pengadaan', function (Blueprint $table) {
            $table->string('pengadaan_id', 100);
            $table->string('trx_id', 100);
            $table->string('reff_trx_id', 100);
            $table->string('jns_transaksi', 50);
            $table->date('tgl_transaksi');
            $table->string('no_transaksi', 100)->nullable();
            $table->date('tgl_dibutuhkan')->nullable();
            $table->string('vendor_id', 100);
            $table->string('vendor_nama', 100);
            
            $table->string('unit_id', 100);
            $table->string('unit_nama', 100);
            
            $table->string('depo_id', 100)->nullable();
            $table->string('depo_nama', 100)->nullable();
            
            $table->string('termin', 5)->nullable();
            $table->string('jenis_bayar', 20)->nullable();
            

            $table->double('subtotal', 18, 2);
            $table->double('total_pajak', 18, 2);
            $table->double('total_diskon', 18, 2);
            $table->double('total', 18, 2);
            $table->text('catatan');
            $table->string('no_invoice', 100)->nullable();
            $table->date('tgl_invoice', 100)->nullable();
            $table->date('tgl_rencana_bayar', 50)->nullable();
            $table->uuid('data_versi');
            $table->string('status', 50);
            $table->string('approved_by', 50)->nullable();
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(array('pengadaan_id', 'trx_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_pengadaan');
    }
};
