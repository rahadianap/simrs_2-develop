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
        Schema::connection('dbclient')->create('tb_trx_operasi_tim', function (Blueprint $table) {
            $table->string('operasi_tim_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('trx_id', 100);
            $table->string('posisi', 100)->nullable();
            $table->string('dokter_id', 100)->nullable();
            $table->string('dokter_nama', 100)->nullable();
            $table->boolean('is_operator')->default(0);
            $table->boolean('is_aktif');
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
        Schema::connection('dbclient')->dropIfExists('tb_trx_operasi_tim');
    }
};
