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
        Schema::connection('dbclient')->create('tb_sub_tindakan', function (Blueprint $table) {
            $table->string('detail_id', 100)->primary();
            $table->string('tindakan_id', 100);
            $table->string('sub_tindakan_id', 100);
            $table->string('tindakan_nama', 100)->nullable();
            $table->string('sub_tindakan_nama', 100)->nullable();
            
            $table->tinyInteger('jumlah')->nullable();
            $table->string('satuan', 100)->nullable();
            
            $table->boolean('is_aktif')->default(0);
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
        Schema::connection('dbclient')->dropIfExists('tb_sub_tindakan');
    }
};
