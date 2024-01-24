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
        Schema::connection('dbclient')->create('tb_referensi', function (Blueprint $table) {
            $table->string('ref_id', 100);
            $table->string('client_id', 100);
            $table->string('ref_group', 100)->nullable();
            $table->string('ref_nama', 100);
            $table->string('deskripsi');
            $table->jsonb('json_data')->nullable();
            $table->boolean('allow_user_edit')->default(0);
            $table->boolean('is_aktif')->default(0);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->primary(['ref_id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_referensi');
    }
};
