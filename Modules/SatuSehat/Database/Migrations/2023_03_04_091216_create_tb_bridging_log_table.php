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
        Schema::connection('dbclient')->create('tb_bridging_log', function (Blueprint $table) {
            $table->string('bridging_log_id', 50)->primary();
            $table->string('bridging_type', 50);
            $table->string('bridging_resource', 50);
            $table->string('bridging_phase', 50);
            $table->string('state', 50);

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
        Schema::dropIfExists('tb_bridging_log');
    }
};
