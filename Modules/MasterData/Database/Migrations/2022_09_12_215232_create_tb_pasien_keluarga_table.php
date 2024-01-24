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
        Schema::connection('dbclient')->create('tb_pasien_keluarga', function (Blueprint $table) {
            $table->string('pasien_id', 100);
            $table->string('status_perkawinan', 100)->nullable();
            $table->boolean('nama_pasangan',100)->nullable();
            $table->tinyText('nama_ayah',100)->nullable();
            $table->string('nama_ibu',100)->nullable();
            $table->string('pekerjaan_pasangan',100)->nullable();
            $table->string('pekerjaan_ayah',100)->nullable();
            $table->string('pekerjaan_ibu',100)->nullable();
            $table->boolean('is_aktif')->default(0);
            $table->string('client_id', 100);
            $table->string('created_by', 100);
            $table->string('updated_by', 100)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_pasien_keluarga');
    }
};
