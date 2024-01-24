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
        Schema::connection('dbclient')->create('tb_pasien_diet_monitoring', function (Blueprint $table) {
            $table->string('pasien_diet_monitoring_id', 100)->primary();
            $table->string('pasien_diet_id', 100);    
            $table->string('trx_id', 100); 
            $table->string('meal_set', 10); 
            $table->tinyText('catatan')->nullable();
            $table->float('skala_karbo', 100); 
            $table->float('skala_sayuran', 100);
            $table->float('skala_lauk', 100); 
            $table->float('skala_buah', 100);
            $table->float('skala_minuman', 100); 
            $table->date('tgl_kontrol');
            $table->string('alasan', 100);
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
        Schema::dropIfExists('tb_pasien_diet_monitoring');
    }
};
