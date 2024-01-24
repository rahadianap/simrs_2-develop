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
        Schema::connection('dbclient')->create('tb_unit_tindakan', function (Blueprint $table) {
            $table->string('unit_tindakan_id', 100);
            $table->string('unit_id', 100);
            $table->string('tindakan_id', 100);
            $table->boolean('is_tampil_dokter')->default(0);

            $table->boolean('is_aktif')->default(0);
            $table->string('client_id', 100);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['unit_tindakan_id', 'unit_id', 'tindakan_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_unit_tindakan');
    }
};
