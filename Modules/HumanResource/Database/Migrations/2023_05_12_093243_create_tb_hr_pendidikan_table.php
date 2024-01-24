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
        Schema::connection('dbclient')->dropIfExists('tb_hr_pendidikan');
        Schema::connection('dbclient')->create('tb_hr_pendidikan', function (Blueprint $table) {
            $table->string('hr_pendidikan_id', 100)->primary();
            $table->string('karyawan_id', 100);
            $table->string('jns_pendidikan', 100);
            $table->string('jenjang', 100)->nullable();
            $table->string('nama_pendidikan',100);
            $table->string('institusi',100);
            $table->date('tahun_lulus');
            $table->string('ipk')->nullable();
            $table->text('catatan')->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_hr_pendidikan');
    }
};
