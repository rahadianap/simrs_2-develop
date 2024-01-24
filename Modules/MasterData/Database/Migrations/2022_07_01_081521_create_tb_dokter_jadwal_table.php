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
        Schema::connection('dbclient')->create('tb_dokter_jadwal', function (Blueprint $table) {
            $table->string('jadwal_id', 100)->primary();
            $table->string('dokter_unit_id', 100);
            $table->string('dokter_id', 100);
            $table->string('unit_id', 100);
            $table->string('hari', 10);
            $table->string('nama_hari', 20)->nullable();
            $table->string('mulai', 10);
            $table->string('selesai', 10);
            $table->boolean('is_ext_app')->default(0);
            $table->smallInteger('kuota_online')->default(0);
            $table->smallInteger('kuota')->default(0);
            $table->tinyInteger('interval_periksa')->default(0);
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
        Schema::connection('dbclient')->dropIfExists('tb_dokter_jadwal');
    }
};
