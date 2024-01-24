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
        Schema::connection('dbclient')->dropIfExists('tb_hr_karyawan');
        Schema::connection('dbclient')->create('tb_hr_karyawan', function (Blueprint $table) {
            $table->string('karyawan_id', 100)->primary();
            $table->string('nama', 100);
            $table->string('nip', 50)->nullable();
            $table->string('jns_kelamin', 2);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('nik', 100)->nullable();
            $table->string('no_telepon', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->date('tgl_masuk');
            $table->string('status_perkawinan',30)->nullable();
            $table->string('jabatan_id',100)->nullable();
            $table->string('jabatan_nama',50)->nullable();
            $table->string('unit_id',100)->nullable();
            $table->string('unit_nama',100)->nullable();
            $table->string('url_foto',200)->nullable();
            $table->text('keahlian')->nullable();
            $table->string('status',30)->nullable();
            $table->tinyText('rekening_gaji')->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_hr_karyawan');
    }
};
