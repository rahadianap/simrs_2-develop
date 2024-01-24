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
        Schema::connection('dbclient')->dropIfExists('tb_jabatan');
        Schema::connection('dbclient')->create('tb_jabatan', function (Blueprint $table) {
            $table->string('jabatan_id', 100)->primary();
            $table->string('jabatan_nama', 100);
            $table->string('deskripsi', 100);
            $table->text('tanggung_jawab')->nullable();
            $table->date('tgl_berlaku')->nullable();
            $table->string('status', 50);
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
        Schema::connection('dbclient')->dropIfExists('tb_jabatan');
    }
};
