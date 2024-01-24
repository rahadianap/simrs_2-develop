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
        Schema::connection('dbclient')->create('tb_lab_hasil', function (Blueprint $table) {
            $table->string('lab_hasil_id', 100)->primary();
            $table->string('hasil_nama', 100);
            $table->string('klasifikasi',100);
            $table->string('sub_klasifikasi',100)->nullable();
            $table->string('no_urut',6)->nullable();
            $table->string('kode_rl',100)->nullable();
            $table->string('header_id',100)->nullable();
            $table->boolean('is_header')->default(0);
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
        Schema::connection('dbclient')->dropIfExists('tb_lab_hasil');
    }
};
