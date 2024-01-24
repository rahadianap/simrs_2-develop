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
        Schema::connection('dbclient')->create('tb_resep_detail', function (Blueprint $table) {
            $table->string('detail_id', 100);
            $table->string('trx_id', 100);
            $table->string('reg_id', 100);
            $table->string('reff_trx_id', 100);
            $table->string('item_id', 100);
            $table->string('item_nama', 100);
            
            $table->string('unit_id', 100)->nullable();
            $table->string('unit_nama', 100)->nullable();
            $table->string('depo_id', 100)->nullable();
            $table->string('depo_nama', 100)->nullable();
            $table->string('dokter_id', 100)->nullable();
            $table->string('dokter_nama', 100)->nullable();
            
            $table->string('jenis_produk', 100)->nullable();
            $table->string('klasifikasi', 100)->nullable();
            $table->string('satuan', 100)->nullable();
            $table->double('jumlah');
            $table->double('jml_ambil')->nullable();
            $table->boolean('is_bhp')->default(0);
            
            $table->boolean('is_racikan')->default(0);
            $table->string('racikan_id', 100)->nullable();
            $table->string('racikan_nama', 100)->nullable();
            $table->string('cara_pakai', 100)->nullable();

            $table->string('signa', 100)->nullable();
            $table->string('signa_deskripsi', 100)->nullable();
            
            $table->decimal('nilai');
            $table->double('diskon_persen');
            $table->decimal('diskon');
            $table->decimal('harga_diskon');
            $table->decimal('subtotal');
            
            $table->text('catatan')->nullable();
            $table->text('status')->nullable();

            $table->boolean('is_aktif');
            $table->boolean('is_realisasi');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['detail_id', 'trx_id', 'reg_id', 'reff_trx_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_resep_detail');
    }
};
