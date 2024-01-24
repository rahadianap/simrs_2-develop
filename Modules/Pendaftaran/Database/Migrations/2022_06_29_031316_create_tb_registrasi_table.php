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
        Schema::connection('dbclient')->create('tb_registrasi', function (Blueprint $table) {
            $table->string('reg_id', 100)->primary();
            $table->dateTime('tgl_registrasi');
            $table->string('jns_registrasi', 100);
            $table->string('cara_registrasi', 100)->nullable();
            $table->string('mode_reg', 20)->nullable();
            $table->date('tgl_periksa');
            $table->time('jam_periksa');
            $table->string('kode_booking', 100)->nullable();
            
            $table->string('no_antrian', 8)->nullable();
            $table->integer('no_urut')->nullable();
            
            $table->string('jadwal_id', 100)->nullable();            
            $table->string('dokter_id', 100);            
            $table->string('unit_id', 100);
            $table->string('ruang_id', 100)->nullable();
            $table->string('bed_id', 100)->nullable();           
            $table->string('dokter_nama', 50)->nullable();
            $table->string('unit_nama', 50)->nullable();
            $table->string('ruang_nama', 50)->nullable(); 

            $table->string('asal_pasien', 30)->nullable();
            $table->string('ket_asal_pasien', 100)->nullable();
            $table->string('pasien_id', 100);
            $table->string('nama_pasien', 100)->nullable();
            $table->string('jns_kelamin', 8)->nullable();
            $table->string('nik', 30)->nullable();
            $table->string('no_kk', 30)->nullable();
            $table->string('no_rm', 20)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('tempat_lahir', 50)->nullable();
            $table->string('usia', 8)->nullable();

            $table->string('jns_penjamin', 20)->nullable();
            $table->string('penjamin_id', 100)->nullable();
            $table->string('penjamin_nama', 100)->nullable();

            $table->string('nama_penanggung', 50)->nullable();
            $table->string('hub_penanggung', 30)->nullable();

            $table->boolean('is_pasien_luar')->default(0);
            $table->boolean('is_pasien_baru')->default(0);
            $table->boolean('is_bpjs')->default(0);

            $table->string('status_reg', 20)->nullable();
            $table->string('status_pulang', 30)->nullable();
            $table->string('cara_pulang', 20)->nullable();
            $table->dateTime('tgl_pulang')->nullable();

            $table->dateTime('tgl_checkin')->nullable();
            $table->boolean('is_checkin')->default(0);
            $table->string('no_pendaftaran', 8)->nullable();

            $table->string('no_sep', 50)->nullable();
            $table->string('no_rujukan', 30)->nullable();
            $table->string('kode_kunjungan', 2)->nullable();
            $table->string('jenis_kunjungan', 50)->nullable();
            $table->tinyText('keterangan_batal', 100)->nullable();
            
            $table->boolean('is_aktif')->default(0);
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
        Schema::connection('dbclient')->dropIfExists('tb_registrasi');
    }
};
