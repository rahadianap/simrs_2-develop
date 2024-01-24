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
        Schema::connection('dbclient')->create('tb_pasien_diet', function (Blueprint $table) {
            $table->string('pasien_diet_id', 100)->primary();    
            $table->string('trx_id', 100); 
            $table->string('pasien_id', 100); 
            $table->string('nama_pasien', 100);
            $table->string('unit_id', 100); 
            $table->string('nama_unit', 100);
            $table->string('dokter_id', 100); 
            $table->string('nama_dokter', 100); 
            $table->date('start_date');
            $table->time('start_time');
            $table->tinyText('catatan')->nullable();
            $table->string('diagnosa', 255); 
            $table->boolean('is_special'); 
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
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
        Schema::dropIfExists('tb_pasien_diet');
    }
};
