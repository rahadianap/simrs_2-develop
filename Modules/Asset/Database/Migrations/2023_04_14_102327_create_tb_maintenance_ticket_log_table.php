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
        Schema::connection('dbclient')->create('tb_maintenance_ticket_log', function (Blueprint $table) {
            $table->string('maint_ticket_log_id', 100)->primary();
            $table->string('maint_ticket_id', 100);
            $table->dateTime('tgl_log');
            $table->string('keterangan', 100);
            $table->string('status', 30);

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
        Schema::dropIfExists('tb_maintenance_ticket_log');
    }
};
