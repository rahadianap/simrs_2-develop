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
        Schema::connection('dbclient')->create('tb_kelurahan', function (Blueprint $table) {
            $table->string('kelurahan_id', 100)->primary();
            $table->string('kecamatan_id', 100);
            $table->string('kota_id', 100);
            $table->string('propinsi_id', 100);
            $table->string('kelurahan', 100);
            $table->string('kecamatan', 100)->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('propinsi', 100)->nullable();
            $table->string('kodepos', 12)->nullable();
            $table->boolean('is_prioritas')->default(0);
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
        Schema::dropIfExists('tb_kelurahan');
    }
};
