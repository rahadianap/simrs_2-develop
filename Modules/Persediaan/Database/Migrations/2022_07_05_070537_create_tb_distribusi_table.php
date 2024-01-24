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
        Schema::connection('dbclient')->create('tb_distribusi', function (Blueprint $table) {
            $table->string('distribusi_id', 100)->primary();
            $table->string('unit_id', 100);
            $table->dateTime('tgl_dibuat');
            $table->dateTime('tgl_dibutuhkan');
            $table->dateTime('tgl_realisasi')->nullable();
            $table->text('catatan');
            $table->string('depo_penerima_id', 100);
            $table->string('depo_pengirim_id', 100);
            $table->string('depo_penerima', 100)->nullable();
            $table->string('depo_pengirim', 100)->nullable();
            $table->string('status', 50);

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('approved_by', 50)->nullable();
            $table->string('received_by', 50)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_distribusi');
    }
};
