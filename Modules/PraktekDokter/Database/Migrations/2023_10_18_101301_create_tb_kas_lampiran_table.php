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
        Schema::connection('dbclient')->create('tb_kas_lampiran', function (Blueprint $table) {
            $table->string('kas_lampiran_id', 100)->primary();
            $table->string('kas_id', 100);
            $table->tinyText('deskripsi')->nullable();
            $table->string('path_lampiran');
            $table->string('url_lampiran');
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
        Schema::connection('dbclient')->dropIfExists('tb_kas_lampiran');
    }
};
