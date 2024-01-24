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
        // Schema::connection('dbclient')->create('tb_bhp', function (Blueprint $table) {
        //     $table->string('detail_id', 100)->primary();
        //     $table->string('reg_id', 100);
        //     $table->string('trx_id', 100);
        //     $table->string('reff_trx_id', 100);
        //     $table->string('item_id', 100);
        //     $table->string('item_nama', 100)->nullable();
        //     $table->string('unit_id', 100);
        //     $table->string('unit_nama', 100)->nullable();
        //     $table->string('depo_id', 100);
        //     $table->string('depo_nama', 100)->nullable();
        //     $table->string('klasifikasi', 100);
        //     $table->string('satuan', 100);
        //     $table->double('jumlah',18,2)->default(0);
        //     $table->boolean('is_realisasi')->default(0);

        //     $table->boolean('is_aktif');
        //     $table->string('client_id', 100);
        //     $table->string('created_by', 50)->nullable();
        //     $table->string('updated_by', 50)->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();

        //     //$table->primary(['detail_id', 'reg_id', 'trx_id', 'sub_trx_id']);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::connection('dbclient')->dropIfExists('tb_bhp');
    }
};
