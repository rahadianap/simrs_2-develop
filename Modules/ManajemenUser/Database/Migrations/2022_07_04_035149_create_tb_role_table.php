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
        Schema::connection('dbclient')->create('tb_role', function (Blueprint $table) {
            $table->string('client_id', 100);
            $table->string('role_id', 100);
            $table->string('role_nama', 50);
            $table->string('deskripsi', 100)->nullable();
            $table->boolean('is_multi_dokter')->default(0);
            $table->boolean('is_ubah_tanggal')->default(0);
            $table->boolean('is_aktif');
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['client_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_role');
    }
};
