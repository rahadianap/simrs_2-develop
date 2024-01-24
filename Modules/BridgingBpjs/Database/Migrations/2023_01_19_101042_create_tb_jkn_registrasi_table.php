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
        Schema::connection('dbclient')->create('tb_jkn_registrasi', function (Blueprint $table) {
            $table->string('client_id', 100);
            $table->string('kode_booking', 100);
            $table->string('reg_id', 100);
            $table->string('pasien_id', 100);
            $table->jsonb('json_request');
            $table->jsonb('json_response');
            $table->boolean('is_aktif')->default(0);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['client_id', 'kode_booking']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_jkn_registrasi');
    }
};
