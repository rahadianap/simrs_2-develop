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
        Schema::connection('dbclient')->create('tb_pengadaan_detail', function (Blueprint $table) {
            $table->string('detail_id', 100);
            $table->string('pengadaan_id', 100);
            $table->string('trx_id', 100);
            $table->string('reff_trx_id', 100);
            $table->string('depo_id', 100);
            
            $table->string('jenis_produk', 30);
            $table->string('produk_id', 100);
            $table->string('produk_nama', 100);
            $table->string('satuan', 20)->nullable();
            $table->string('satuan_beli', 20);
            $table->float('konversi');
            $table->smallInteger('jml_po');
            $table->smallInteger('jml_total_po');
            $table->smallInteger('jml_por');
            $table->smallInteger('jml_total_por');
            $table->smallInteger('jml_porr');
            $table->smallInteger('jml_total_porr');

            $table->float('harga');
            $table->float('diskon');
            $table->float('nilai_diskon');
            $table->float('subtotal');
            $table->boolean('is_pajak');
            $table->float('nilai_pajak');
            $table->float('persen_pajak');
            
            $table->string('status', 50);

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
        Schema::connection('dbclient')->dropIfExists('tb_pengadaan_detail');
    }
};
