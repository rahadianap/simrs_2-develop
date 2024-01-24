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
        /**TIDAK DIGUNAKAN (OBSOLETE) */
        // Schema::connection('dbclient')->create('tb_lab_group', function (Blueprint $table) {
        //     $table->string('lab_grup_id', 100)->primary();
        //     $table->string('lab_grup', 100);

        //     $table->boolean('is_aktif');
        //     $table->string('client_id', 100);
        //     $table->string('created_by', 50)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_lab_group');
    }
};
