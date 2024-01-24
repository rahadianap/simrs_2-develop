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
        Schema::connection('dbclient')->create('tb_maintenance_ticket', function (Blueprint $table) {
            $table->string('maint_ticket_id', 100)->primary();
            $table->string('asset_id', 100);
            $table->string('jenis_maintenance', 100);
            $table->dateTime('tgl_tiket');
            $table->string('keterangan', 100);
            $table->string('prioritas', 10);
            $table->string('lokasi_asset', 100);
            $table->string('nama_teknisi', 100);
            $table->string('tindakan');
            $table->tinyText('catatan_tindakan');
            $table->dateTime('tgl_selesai');
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
        Schema::dropIfExists('tb_maintenance_ticket');
    }
};
