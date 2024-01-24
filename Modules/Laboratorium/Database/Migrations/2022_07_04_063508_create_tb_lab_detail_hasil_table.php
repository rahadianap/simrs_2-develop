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
        /**OBSOLETE (TIDAK DIGUNAKAN) */
        // Schema::connection('dbclient')->create('tb_lab_detail_hasil', function (Blueprint $table) {
        //     $table->string('detail_id', 100);
        //     $table->string('reg_id', 100);
        //     $table->string('trx_id', 100);
        //     $table->string('sub_trx_id', 100);
        //     $table->string('item_id', 100); // kode tindakan (dari trx_detail)
        //     $table->string('item_nama', 100)->nullable(); // nama tindakan (dari trx_detail)
        //     $table->string('hasil_id', 100); //dari lab template
        //     $table->string('header_id', 100)->nullable();
        //     $table->boolean('is_header')->nullable();
            
        //     $table->string('normal_group', 100)->nullable();
        //     $table->string('hasil_nama', 100)->nullable();
        //     $table->string('no_urut', 100)->nullable();
        //     $table->string('jenis_hasil', 100)->nullable();
        //     $table->string('operator_hasil', 100)->nullable();
        //     $table->float('nilai_min')->nullable();
        //     $table->float('nilai_maks')->nullable();
        //     $table->string('nilai_pilihan', 100)->nullable();
        //     $table->string('formula_nilai', 100)->nullable();
        //     $table->string('nilai_normal', 100)->nullable();
        //     $table->string('lab_satuan', 100)->nullable();
        //     $table->string('nilai_pemeriksaan', 100)->nullable();
        //     $table->boolean('is_tandai')->nullable();

        //     $table->boolean('is_aktif');
        //     $table->string('client_id', 100);
        //     $table->string('created_by', 50)->nullable();
        //     $table->string('updated_by', 50)->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();

        //     $table->primary(['detail_id', 'reg_id', 'trx_id', 'sub_trx_id']);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_lab_detail_hasil');
    }
};
