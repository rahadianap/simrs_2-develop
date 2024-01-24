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
        Schema::connection('dbclient')->create('tb_lab_normal', function (Blueprint $table) {
            $table->string('lab_normal_id', 100)->primary();
            $table->string('lab_hasil_id', 100);
            $table->string('normal_group', 100);            
            $table->string('hasil_nama', 100)->nullable();
            $table->string('jenis_hasil', 100);
            
            $table->string('lk_operator', 50)->nullable();
            $table->string('lk_nilai_min', 50)->nullable();
            $table->string('lk_nilai_maks', 50)->nullable();
            $table->string('lk_nilai_pilihan', 50)->nullable();
            $table->string('lk_normal', 200)->nullable();
            
            $table->string('pr_operator', 50)->nullable();
            $table->string('pr_nilai_min', 50)->nullable();
            $table->string('pr_nilai_maks', 50)->nullable();
            $table->string('pr_nilai_pilihan', 50)->nullable();
            $table->string('pr_normal', 200)->nullable();
            
            $table->string('satuan', 100)->nullable();
            
            $table->boolean('is_aktif')->default(0);
            $table->string('client_id', 100);
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_lab_normal');
    }
};
