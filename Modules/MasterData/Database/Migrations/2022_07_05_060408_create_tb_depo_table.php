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
        Schema::connection('dbclient')->create('tb_depo', function (Blueprint $table) {
            $table->string('depo_id', 100)->primary();
            $table->string('depo_nama', 100);
            $table->string('deskripsi', 100);
            $table->string('lokasi', 100)->nullable();
            $table->boolean('is_gudang');
            $table->boolean('is_opname');
            $table->boolean('is_virtual');
            $table->boolean('is_lock');

            $table->boolean('is_stock_adjustment')->default(0);
            $table->boolean('is_inventory_issue')->default(0);
            $table->boolean('is_distribution')->default(0);
            $table->boolean('is_purchase_req')->default(0);
            $table->boolean('is_purchase_order')->default(0);
            $table->boolean('is_purchase_receive')->default(0);
            $table->boolean('is_purchase_return')->default(0);
            $table->boolean('is_direct_purchase')->default(0);
            $table->boolean('is_production')->default(0);
            $table->boolean('is_sell')->default(0);

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_depo');
    }
};
