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
        Schema::connection('dbclient')->create('tb_darah_terima', function (Blueprint $table) {
            $table->string('darah_terima_id', 100)->primary();            
            $table->string('asal_darah', 100);
            $table->string('nama_donor', 100)->nullable();           
            $table->date('tgl_terima');            
            $table->string('penerima', 100)->nullable();
            $table->string('status', 100)->nullable();
            $table->tinyText('catatan')->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_darah_terima');
    }
};
