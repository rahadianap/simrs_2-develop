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
        Schema::connection('dbclient')->create('tb_harga_log', function (Blueprint $table) {
            $table->string('harga_log_id', 100)->primary();
            $table->string('harga_id',100);
            $table->string('buku_harga_id',100);
            $table->string('buku_nama',100)->nullable();
            
            $table->string('tindakan_id', 100);
            $table->string('tindakan_nama', 100)->nullable();
            $table->string('kelas_id', 100);
            $table->string('kelas_nama', 100)->nullable();
            $table->double('nilai_lama', 18, 2)->nullable();
            $table->double('nilai', 18, 2)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_harga_log');
    }
};
