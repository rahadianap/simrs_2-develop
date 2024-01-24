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
        Schema::connection('dbclient')->create('tb_pasien_diet_detail', function (Blueprint $table) {
            $table->string('pasien_diet_detail_id', 100)->primary();
            $table->string('pasien_diet_id', 100);     
            $table->string('trx_id', 100); 
            $table->string('diet_id', 100); 
            $table->string('nama_diet', 100);
            $table->string('menu_id', 100); 
            $table->string('nama_menu', 100);
            $table->string('qty', 100); 
            $table->string('schedule', 100); 
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
        Schema::dropIfExists('tb_pasien_diet_detail');
    }
};
