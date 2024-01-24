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
        Schema::connection('dbclient')->create('tb_unit_otorisasi', function (Blueprint $table) {
            $table->string('unit_id', 100)->primary();
            $table->boolean('is_unit_kiosk')->default(0);
            $table->boolean('is_unit_external')->default(0);
            $table->boolean('is_pilih_dokter')->default(1);
            $table->boolean('is_registrasi')->default(0);
            $table->boolean('is_rm_baru')->default(0);
            $table->boolean('is_unit_rujukan')->default(0);
            $table->boolean('is_purchase_req')->default(0);
            $table->boolean('is_purchase_order')->default(0);
            $table->boolean('is_purchase_receive')->default(0);
            $table->boolean('is_purchase_return')->default(0);
            $table->boolean('is_direct_purchase')->default(0);
            $table->boolean('is_stock_adjustment')->default(0);
            $table->boolean('is_inventory_issue')->default(0);
            $table->boolean('is_produksi')->default(0);
            $table->boolean('is_distribusi')->default(0);
            $table->boolean('is_bedah')->default(0);
            $table->boolean('is_aktif')->default(0);
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_unit_otorisasi');
    }
};
