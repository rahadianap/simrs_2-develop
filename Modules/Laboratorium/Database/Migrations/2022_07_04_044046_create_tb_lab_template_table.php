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
        Schema::connection('dbclient')->create('tb_lab_template', function (Blueprint $table) {
            $table->string('lab_template_id', 100)->primary();
            $table->string('tindakan_id', 100);
            $table->string('lab_hasil_id', 100);            
            $table->string('tindakan_nama', 100)->nullable();
            $table->string('hasil_nama', 100)->nullable();            
            $table->string('klasifikasi', 100)->nullable(); 
            $table->string('sub_klasifikasi', 100)->nullable(); 
            $table->string('no_urut', 4)->nullable();
            $table->string('level', 5)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_lab_template');
    }
};
