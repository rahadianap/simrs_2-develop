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
        Schema::connection('dbclient')->create('tb_donasi_detail', function (Blueprint $table) {
            $table->string('donasi_detail_id', 100);
            $table->string('donasi_id', 100);
            $table->string('depo_id', 100);
            $table->string('produk_id', 100);
            $table->string('satuan_id', 100);
            $table->smallInteger('jml_diterima');
            $table->string('status', 50);

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(array('donasi_detail_id', 'donasi_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_donasi_detail');
    }
};
