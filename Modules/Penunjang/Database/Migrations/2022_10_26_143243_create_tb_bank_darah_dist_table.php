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
        Schema::connection('dbclient')->create('tb_darah_dist', function (Blueprint $table) {
            $table->string('darah_dist_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('jns_registrasi', 100)->nullable();
            
            $table->string('pasien_id', 100);
            $table->string('pasien_nama', 100);
            $table->string('no_rm', 100)->nullable();

            $table->string('unit_id', 100);
            $table->string('unit_nama', 100);
            $table->string('ruang_id', 100)->nullable();
            $table->string('ruang_nama', 100)->nullable();
            $table->string('bed_no', 10)->nullable();
            $table->string('dokter_id', 100)->nullable();
            $table->string('dokter_nama', 10)->nullable();
            
            
            $table->string('penjamin_id', 100)->nullable();
            $table->string('jns_penjamin', 100)->nullable();
            $table->string('penjamin_nama', 100)->nullable();
            $table->string('usia', 50)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('jenis_kelamin', 2)->nullable();
            
            $table->date('tgl_permintaan');
            $table->time('jam_permintaan');
            $table->string('peminta', 100)->nullable();

            $table->date('tgl_dibutuhkan');
            $table->time('jam_dibutuhkan');
            
            $table->date('tgl_distribusi')->nullable();
            $table->time('jam_distribusi')->nullable();
            $table->string('pengirim', 100)->nullable();
            $table->string('penerima', 100)->nullable();
           
            $table->string('gol_darah', 2);
            $table->string('group_darah', 100)->nullable();
            $table->string('rhesus', 10)->nullable();
            $table->integer('haemoglobin')->nullable();
            $table->integer('jml_permintaan');
            $table->integer('volume_per_labu');
            
            $table->string('diagnosa', 100)->nullable();
            $table->boolean('is_kirim')->default(0);
            
            $table->string('status', 100)->nullable();
            $table->tinyText('catatan')->nullable();
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
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
        Schema::connection('dbclient')->dropIfExists('tb_darah_dist');
    }
};
