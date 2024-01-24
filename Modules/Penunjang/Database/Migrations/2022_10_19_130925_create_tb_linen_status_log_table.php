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
        Schema::connection('dbclient')->create('tb_linen_status_log', function (Blueprint $table) {
            $table->string('linen_status_id', 100)->primary();
            $table->string('trx_linen_id', 100);
            $table->date('tgl_status');
            $table->time('jam_status');
            $table->string('status', 100);  
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
        Schema::connection('dbclient')->dropIfExists('tb_linen_status_log');
    }
};
