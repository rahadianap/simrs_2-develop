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
        Schema::connection('dbclient')->create('tb_produk_akun', function (Blueprint $table) {
            $table->string('produk_akun_id', 100)->primary();
            $table->string('produk_akun', 100);
            $table->string('jenis_produk',100);
            $table->string('coa_revenue_id',100)->nullable();
            $table->string('coa_revenue',100)->nullable();
            $table->string('akun_revenue',100)->nullable();
            $table->string('coa_inventory_id',100)->nullable();
            $table->string('coa_inventory',100)->nullable();
            $table->string('akun_inventory',100)->nullable();
            $table->string('coa_cogs_id',100)->nullable();
            $table->string('coa_cogs',100)->nullable();
            $table->string('akun_cogs',100)->nullable();
            $table->boolean('is_aktif')->default(0);
            $table->string('client_id', 100);
            $table->string('created_by', 100);
            $table->string('updated_by', 100)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_produk_akun');
    }
};
