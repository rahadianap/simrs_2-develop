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
        Schema::connection('dbclient')->create('tb_penyesuaian_detail', function (Blueprint $table) {
            $table->string('penyesuaian_detail_id', 100);
            $table->string('penyesuaian_id', 100);
            $table->string('depo_id', 100);
            $table->string('depo_nama', 100)->nullable();
            $table->string('produk_id', 100);
            $table->string('produk_nama', 100)->nullable();
            $table->string('satuan', 100)->nullable();
            $table->smallInteger('jml_awal');
            $table->smallInteger('jml_penyesuaian');
            $table->smallInteger('jml_akhir');
            $table->string('status', 50);

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(array('penyesuaian_detail_id', 'penyesuaian_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_penyesuaian_detail');
    }
};
