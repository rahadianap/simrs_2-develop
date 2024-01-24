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
        Schema::connection('dbclient')->create('tb_makanan', function (Blueprint $table) {
            $table->string('makanan_id', 100)->primary();
            $table->string('nama_makanan', 100);
            $table->string('kelompok', 100);
            $table->string('jns_makanan', 100);
            $table->tinyText('catatan')->nullable();
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
        Schema::dropIfExists('tb_makanan');
    }
};
