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
        Schema::connection('dbclient')->create('tb_dokter_unit', function (Blueprint $table) {
            $table->string('dokter_unit_id', 100)->primary();
            $table->string('dokter_id', 100);
            $table->string('unit_id', 100);
            $table->string('ruang_id', 100)->nullable();
            $table->string('prefix_no_antrian', 5)->nullable();

            $table->boolean('is_aktif')->default(0);
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
        Schema::connection('dbclient')->dropIfExists('tb_dokter_unit');
    }
};
