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
        Schema::connection('dbclient')->create('tb_member_unit', function (Blueprint $table) {
            $table->string('member_unit_id', 100)->primary();
            $table->string('user_id', 100);
            $table->string('client_id', 100);
            $table->string('unit_id', 100);
            $table->string('unit_nama', 100)->nullable();

            $table->boolean('is_aktif');
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
        Schema::connection('dbclient')->dropIfExists('tb_member_unit');
    }
};
