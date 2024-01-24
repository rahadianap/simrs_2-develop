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
        Schema::connection('dbclient')->create('tb_icd9', function (Blueprint $table) {
            $table->string('kode_icd', 100);
            $table->string('client_id', 100);
            $table->string('nama_icd', 100);
            $table->string('kata_kunci', 100)->nullable();
            $table->boolean('is_valid_icd')->default(0);

            $table->boolean('is_aktif')->default(0);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['kode_icd', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_icd9');
    }
};
