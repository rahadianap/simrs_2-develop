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
        Schema::connection('dbclient')->create('tb_asesmen_detail', function (Blueprint $table) {
            $table->string('asesmen_detail_id', 100)->primary();
            $table->string('asesmen_id', 100);
            $table->string('pertanyaan', 100);
            $table->string('jenis_jawaban', 100);
            $table->string('pilihan', 100)->nullable();

            $table->boolean('is_aktif');
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
        Schema::connection('dbclient')->dropIfExists('tb_asesmen_detail');
    }
};
