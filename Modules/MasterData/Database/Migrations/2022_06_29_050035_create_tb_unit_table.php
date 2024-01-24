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
        Schema::connection('dbclient')->create('tb_unit', function (Blueprint $table) {
            $table->string('unit_id', 100)->primary();
            $table->string('inisial', 50)->nullable();
            $table->string('unit_nama', 50);
            $table->string('kepala_unit', 100)->nullable();
            $table->string('jenis_registrasi', 100)->nullable();            
            $table->string('group_unit', 100)->nullable();            
            $table->string('icon_dir', 100)->nullable();
            $table->string('icon_url', 100)->nullable();
            
            $table->boolean('is_unit_kiosk')->default(0);
            $table->boolean('is_unit_external')->default(0);
            $table->boolean('is_pilih_dokter')->default(0);
            $table->boolean('is_registrasi')->default(0);
            $table->boolean('is_rm_baru')->default(0);
            $table->boolean('is_unit_rujukan')->default(0);
            $table->boolean('is_bedah')->default(0);
            
            $table->boolean('is_aktif')->default(0);

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
        Schema::connection('dbclient')->dropIfExists('tb_unit');
    }
};
