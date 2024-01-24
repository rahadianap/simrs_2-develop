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
        Schema::connection('dbclient')->create('tb_coa', function (Blueprint $table) {
            $table->string('coa_id',100)->primary();
            $table->string('kode_coa',100);
            $table->string('nama_coa',100);
            $table->string('level_coa',2);
            $table->string('level_nama',50);
            $table->string('coa_header',100)->nullable();
            $table->string('coa_header_id',100)->nullable();
            $table->string('nilai_normal',10);
            $table->string('text_coa',100);
            
            $table->Boolean('is_valid_coa')->default(0);
            $table->Boolean('is_aktif')->default(0);
            $table->string('client_id',100);
            $table->string('created_by', 50)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_coa');
    }
};
