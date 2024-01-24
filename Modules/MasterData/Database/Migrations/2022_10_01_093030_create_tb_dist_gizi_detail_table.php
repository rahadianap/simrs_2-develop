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
        Schema::connection('dbclient')->create('tb_dist_gizi_detail', function (Blueprint $table) {
            $table->string('detail_id', 100)->primary();
            $table->string('dist_gizi_id', 100);
            $table->date('tgl_permintaan');
            $table->date('tgl_dibutuhkan');
            $table->string('reg_id', 100);
            $table->string('trx_id', 100);
            $table->string('unit_id', 100);
            $table->string('ruang_id', 100);
            $table->string('diet_id', 100);
            $table->string('kelas_id', 100);
            $table->string('bed_id', 100);
            $table->string('bed_no', 100);
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
        Schema::dropIfExists('tb_dist_gizi_detail');
    }
};
