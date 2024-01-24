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
        Schema::connection('dbclient')->create('tb_cssd_terima', function (Blueprint $table) {
            $table->string('cssd_terima_id', 100)->primary();
            
            $table->string('unit_pengirim_id', 100);
            $table->string('unit_pengirim', 100)->nullable();
            $table->string('unit_penerima_id', 100)->nullable();
            $table->string('unit_penerima', 100)->nullable();
            
            $table->string('pengirim', 100)->nullable();
            $table->string('penerima', 100)->nullable();
            $table->date('tgl_terima');
            $table->time('jam_terima');
            
            $table->date('tgl_selesai')->nullable();
            $table->time('jam_selesai')->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_cssd_terima');
    }
};
