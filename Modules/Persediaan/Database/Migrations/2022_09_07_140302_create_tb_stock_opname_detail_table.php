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
        Schema::connection('dbclient')->create('tb_stock_opname_detail', function (Blueprint $table) {
            $table->string('so_detail_id', 100);
            $table->string('so_id', 100);
            $table->string('depo_id', 100);
            $table->string('produk_id', 100);
            $table->string('depo_nama', 100);
            $table->string('produk_nama', 100);
            $table->double('total_awal', 18, 2);
            $table->double('total_so', 18, 2);
            $table->double('selisih', 18, 2);
            $table->string('status', 50);
            $table->string('jenis_produk', 50);

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(array('so_detail_id', 'so_id', 'depo_id', 'produk_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_stock_opname_detail');
    }
};
