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
        Schema::connection('dbclient')->create('tb_trx_lab_hasil', function (Blueprint $table) {            
            $table->string('trx_lab_hasil_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('trx_id', 100);
            $table->string('detail_id', 100); //dari tabel trx_detail            
            $table->string('item_id', 100); // dari table trx_detail (lab template)
            $table->string('item_nama', 100); // dari table trx_detail (lab template)            
            $table->string('lab_hasil_id', 100); // dari table lab_hasil
            $table->string('hasil_nama', 100)->nullable();
            $table->string('klasifikasi', 100);
            $table->string('sub_klasifikasi', 100)->nullable();            
            $table->string('normal_group', 100);
            $table->string('jns_kelamin', 20);
            $table->boolean('is_header')->default(0);
            $table->string('no_urut',5)->nullable();
            //hasil pengisian dari master data
            $table->string('hasil_nilai', 50)->nullable();
            $table->string('hasil_operator', 50)->nullable();
            $table->string('hasil_pilihan', 50)->nullable();
            $table->float('hasil_min', 50)->nullable();
            $table->float('hasil_maks', 50)->nullable();
            $table->string('hasil_normal_pr', 100)->nullable();
            $table->string('hasil_normal_lk', 100)->nullable();
            $table->string('jenis_hasil', 100);
            $table->string('satuan', 100)->nullable();            
            $table->boolean('is_tandai')->default(0);
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
        Schema::connection('dbclient')->dropIfExists('tb_trx_lab_hasil');
    }
};
