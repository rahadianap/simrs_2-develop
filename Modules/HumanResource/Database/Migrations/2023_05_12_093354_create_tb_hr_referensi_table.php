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
        Schema::connection('dbclient')->dropIfExists('tb_hr_referensi');
        Schema::connection('dbclient')->create('tb_hr_referensi', function (Blueprint $table) {
            $table->string('hr_referensi_id', 100)->primary();
            $table->string('reff_nama', 100);
            $table->string('deskripsi', 100);
            $table->jsonb('reff_data')->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_hr_referensi');
    }
};
