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
        Schema::connection('dbclient')->create('tb_kota', function (Blueprint $table) {
            $table->string('kota_id', 100)->primary();
            $table->string('propinsi_id', 100);
            $table->string('kota', 100);
            $table->string('propinsi', 100)->nullable();
            $table->string('jenis', 20)->nullable();
            $table->boolean('is_aktif')->default(0);
            $table->boolean('is_prioritas')->default(0);
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
        Schema::dropIfExists('tb_kota');
    }
};
