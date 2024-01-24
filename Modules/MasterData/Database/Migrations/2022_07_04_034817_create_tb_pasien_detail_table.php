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
        Schema::connection('dbclient')->create('tb_pasien_detail', function (Blueprint $table) {
            $table->string('pasien_id', 100);
            $table->string('gol_darah', 100)->nullable();
            $table->string('rhesus', 20)->nullable();
            $table->string('pendidikan', 50)->nullable();
            $table->string('pekerjaan', 50)->nullable();
            $table->string('agama', 20)->nullable();
            $table->string('kebangsaan', 20)->nullable();
            $table->string('suku_bangsa', 20)->nullable();
            $table->string('no_telepon', 15)->nullable();
            $table->string('alamat_email', 100)->nullable();
            $table->string('no_kontak_darurat', 100)->nullable();
            $table->string('nama_kontak_darurat', 100)->nullable();
            $table->string('hub_kontak_darurat', 100)->nullable();

            $table->boolean('is_aktif')->default(0);
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->primary(['pasien_id','client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_pasien_detail');
    }
};
