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
        Schema::connection('dbclient')->create('tb_bridging', function (Blueprint $table) {
            $table->string('bridging_id', 50)->primary();
            $table->string('bridging_group', 50);
            $table->string('local_resource_id', 50);
            $table->string('local_resource_name', 50);
            $table->string('bridging_resource_id', 50);
            $table->string('bridging_resource_name', 50);
            $table->string('bridging_sub_resource_id', 50)->nullable();
            $table->string('bridging_sub_resource_name', 50)->nullable();

            $table->boolean('is_aktif')->default(0);

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
        Schema::dropIfExists('tb_bridging');
    }
};
