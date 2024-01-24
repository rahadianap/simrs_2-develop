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
        /**
         * OBSOLETE
         * **/
        // Schema::connection('dbclient')->create('tb_lab_normal_group', function (Blueprint $table) {
        //     $table->string('normal_group_id', 100)->primary();
        //     $table->string('normal_group', 100);
        //     $table->tinyText('deskripsi');
        //     $table->boolean('is_semua_umur')->nullable();
        //     $table->tinyInteger('usia_min')->nullable();
        //     $table->tinyInteger('usia_maks')->nullable();

        //     $table->boolean('is_aktif');
        //     $table->string('client_id', 100);
        //     $table->string('created_by', 50);
        //     $table->string('updated_by', 50)->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::connection('dbclient')->dropIfExists('tb_lab_normal_group');
    }
};
