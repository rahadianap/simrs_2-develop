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
        Schema::connection('dbclient')->dropIfExists('tb_hr_dokumen');
        Schema::connection('dbclient')->create('tb_hr_dokumen', function (Blueprint $table) {
            $table->string('hr_dokumen_id', 100)->primary();
            $table->string('karyawan_id', 100);
            $table->string('jenis_dokumen', 100);
            $table->text('catatan')->nullable();
            $table->string('url_dokumen', 200)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_hr_dokumen');
    }
};
