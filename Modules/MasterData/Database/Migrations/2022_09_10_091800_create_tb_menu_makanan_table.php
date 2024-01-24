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
        Schema::connection('dbclient')->create('tb_menu_makanan', function (Blueprint $table) {
            $table->string('menu_makanan_id', 100)->primary();
            $table->string('makanan_id', 100);
            $table->string('menu_id', 100);
            $table->string('nama_makanan', 100);
            $table->string('nama_menu', 100);
            $table->double('jumlah', 18, 2);
            $table->string('satuan', 100);
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 100);
            $table->string('updated_by', 100);
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
        Schema::dropIfExists('tb_menu_makanan');
    }
};
