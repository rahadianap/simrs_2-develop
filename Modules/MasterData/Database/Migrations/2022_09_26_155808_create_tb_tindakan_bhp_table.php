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
        Schema::connection('dbclient')->create('tb_tindakan_bhp', function (Blueprint $table) {
            $table->string('tindakan_bhp_id', 100)->primary();
            $table->string('tindakan_id', 100);
            $table->string('produk_id',100);
            $table->string('tindakan_nama',100)->nullable();
            $table->string('produk_nama',100)->nullable();
            $table->string('jenis_produk',30)->nullable();
            $table->float('jumlah')->default(0);
            $table->string('satuan',100)->nullable();
            $table->boolean('is_aktif')->default(0);
            $table->string('client_id', 100);
            $table->string('created_by', 100)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_tindakan_bhp');
    }
};
