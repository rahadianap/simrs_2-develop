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
        Schema::connection('dbclient')->create('tb_keluarga_jenazah', function (Blueprint $table) {
            $table->string('keluarga_id', 100);
            $table->string('reg_id', 100);
            $table->string('trx_id', 100);
            $table->string('sub_trx_id', 100)->nullable();
            $table->string('nama', 100);
            $table->string('hubungan', 100);
            $table->string('nik', 20)->nullable();
            $table->string('no_telepon', 15)->nullable();

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['keluarga_id', 'reg_id', 'trx_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_keluarga_jenazah');
    }
};
