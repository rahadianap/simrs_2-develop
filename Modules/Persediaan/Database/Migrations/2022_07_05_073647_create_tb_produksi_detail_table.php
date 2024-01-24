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
        Schema::connection('dbclient')->create('tb_produksi_detail', function (Blueprint $table) {
            $table->string('produksi_detail_id', 100);
            $table->string('produksi_id', 100);
            $table->string('depo_id', 100);
            $table->string('produk_id', 100);
            $table->string('produk_nama', 100)->nullable();
            $table->string('satuan_id', 100);
            $table->boolean('is_hasil');
            $table->float('jml_hasil');
            $table->float('jml_bahan');
            $table->string('status', 50);

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(array('produksi_detail_id', 'produksi_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_produksi_detail');
    }
};
