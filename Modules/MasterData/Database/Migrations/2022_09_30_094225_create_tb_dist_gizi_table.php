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
        Schema::connection('dbclient')->create('tb_dist_gizi', function (Blueprint $table) {
            $table->string('dist_gizi_id', 100)->primary();
            $table->date('tgl_permintaan');
            $table->date('tgl_dibutuhkan');
            $table->string('unit_id', 100);
            $table->tinyText('catatan')->nullable();
            $table->string('status', 50);
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 100);
            $table->string('updated_by', 100);
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
        Schema::dropIfExists('tb_dist_gizi');
    }
};
