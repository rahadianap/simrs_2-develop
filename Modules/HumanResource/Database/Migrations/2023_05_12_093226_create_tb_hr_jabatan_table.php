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
        Schema::connection('dbclient')->dropIfExists('tb_hr_jabatan');
        Schema::connection('dbclient')->create('tb_hr_jabatan', function (Blueprint $table) {
            $table->string('hr_jabatan_id', 100)->primary();
            $table->string('karyawan_id', 100);
            $table->string('jabatan_id', 100);
            $table->string('jabatan_nama', 100);
            $table->string('unit_id',100)->nullable();
            $table->string('unit_nama',100)->nullable();
            $table->date('tgl_mulai');
            $table->date('tgl_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_selesai')->default(0);
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
        Schema::connection('dbclient')->dropIfExists('tb_hr_jabatan');
    }
};
