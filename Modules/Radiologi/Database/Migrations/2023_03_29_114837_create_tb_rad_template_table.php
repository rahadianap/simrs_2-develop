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
        Schema::connection('dbclient')->create('tb_rad_template', function (Blueprint $table) {
            $table->string('rad_template_id', 100)->primary();
            $table->string('rad_template_nama', 100);
            $table->string('deskripsi', 100)->nullable();
            $table->tinyText('kesan');
            $table->tinyText('uraian');
            $table->tinyText('catatan');
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
        Schema::connection('dbclient')->dropIfExists('tb_rad_template');
    }
};
