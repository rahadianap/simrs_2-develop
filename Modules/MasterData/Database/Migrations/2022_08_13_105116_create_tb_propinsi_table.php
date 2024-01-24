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
        Schema::connection('dbclient')->create('tb_propinsi', function (Blueprint $table) {
            $table->string('propinsi_id', 100)->primary();
            $table->string('propinsi', 100);
            $table->string('no_urut', 100)->nullable();
            $table->boolean('is_aktif')->default(0);
            $table->boolean('is_prioritas')->nullable();
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
        Schema::dropIfExists('tb_propinsi');
    }
};
