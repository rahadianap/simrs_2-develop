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
        Schema::connection('dbclient')->create('tb_distribusi_detail', function (Blueprint $table) {
            $table->string('distribusi_detail_id', 100);
            $table->string('distribusi_id', 100);
            $table->string('produk_id');
            $table->string('satuan_id');
            $table->smallInteger('jml_diminta');
            $table->smallInteger('jml_dikirim')->default(0);
            $table->string('status', 50);
            $table->string('pr_id', 100)->nullable();
            $table->string('po_id', 100)->nullable();
            $table->string('depo_pengirim_id', 100)->nullable();
            $table->string('depo_penerima_id', 100)->nullable();
            $table->string('depo_pengirim', 100)->nullable();
            $table->string('depo_penerima', 100)->nullable();
            $table->string('depo_produk_id', 100)->nullable();
            $table->string('produk_nama', 100)->nullable();
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(array('distribusi_detail_id', 'distribusi_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_distribusi_detail');
    }
};
