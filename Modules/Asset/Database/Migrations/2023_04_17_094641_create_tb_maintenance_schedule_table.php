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
        Schema::connection('dbclient')->create('tb_maintenance_schedule', function (Blueprint $table) {
            $table->string('maint_schedule_id', 100)->primary();
            $table->string('teknisi', 100);
            $table->date('tgl_rencana');
            $table->string('nama_vendor', 100);
            $table->string('status', 100);
            $table->date('tgl_realisasi')->nullable();
            $table->string('catatan', 100)->nullable();
            $table->string('tindak_lanjut', 100)->nullable();
            $table->boolean('is_vendor')->nullable();

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
        Schema::dropIfExists('tb_maintenance_schedule');
    }
};
