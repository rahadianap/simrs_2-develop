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
        Schema::connection('dbclient')->create('tb_pemakaian_bed', function (Blueprint $table) {
            $table->string('pemakaian_bed_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('trx_id', 100);
            
            $table->string('unit_id', 100);
            $table->string('unit_nama', 100);
            $table->string('ruang_id', 100);
            $table->string('ruang_nama', 100);
            $table->string('bed_id', 100);
            $table->string('no_bed', 100);
            
            $table->string('unit_asal_id', 100)->nullable();
            $table->string('unit_asal', 100)->nullable();
            $table->string('ruang_asal_id', 100)->nullable();
            $table->string('ruang_asal', 100)->nullable();
            $table->string('bed_asal_id', 100)->nullable();
            $table->string('no_asal_bed', 20)->nullable();
            
            $table->tinyText('catatan')->nullable();
            
            $table->date('tgl_masuk');
            $table->time('jam_masuk');
            
            $table->date('tgl_keluar')->nullable();
            $table->time('jam_keluar')->nullable();
            
            $table->string('penjamin_id', 100)->nullable();
            $table->string('penjamin_nama', 100)->nullable();
            
            $table->string('buku_harga_id', 100)->nullable();
            $table->string('buku_nama', 100)->nullable();
            $table->string('kelas_harga', 100)->nullable();
            $table->string('harga_id', 100)->nullable();
            $table->string('harga_nama', 100)->nullable();

            $table->string('pasien_id', 100);
            $table->string('no_rm', 100);
            $table->string('nama_pasien', 100);
           
            $table->smallInteger('jml_hari');
            $table->boolean('is_manual')->default(0);
            $table->boolean('is_keluar')->default(0);
            $table->boolean('is_pulang')->default(0);
            $table->string('status',20)->nullable();

            $table->boolean('is_aktif');
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
        Schema::connection('dbclient')->dropIfExists('tb_pemakaian_bed');
    }
};
