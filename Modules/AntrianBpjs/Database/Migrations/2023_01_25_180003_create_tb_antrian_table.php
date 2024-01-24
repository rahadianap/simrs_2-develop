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
        //OBSOLETE
        // Schema::connection('dbclient')->create('tb_antrian', function (Blueprint $table) {
        //     /**
        //      * digunakan untuk menyimpan semua kode booking
        //      */
        //     $table->string('client_id', 100);
        //     $table->string('antrian_id', 100);
        //     $table->string('reg_id', 100);
        //     $table->string('trx_id', 100)->nullable();
        //     $table->string('sub_trx_id', 100)->nullable();
        //     $table->string('jns_registrasi',20); //LAB ,RAD, RAWAT JALAN, dsbnya
        //     $table->dateTime('tgl_registrasi');
        //     $table->date('tgl_periksa');
        //     $table->time('jam_periksa');
        //     $table->string('jadwal_id')->nullable();
        //     $table->string('dokter_id')->nullable();
        //     $table->string('unit_id')->nullable();
        //     $table->string('no_antrian', 6); //antrian poli
        //     $table->integer('angka_antrian');
        //     $table->string('no_pendaftaran', 6)->nullable(); //antrian pendaftaran->dibuat saat check in
        //     $table->integer('angka_pendaftaran')->nullable();

        //     $table->string('mode_reg', 10); //WALK IN atau BOOKING
        //     $table->string('pasien_id', 100);
        //     $table->string('jns_penjamin', 20);
            
        //     $table->dateTime('tgl_checkin')->nullable();
        //     $table->boolean('is_checkin')->default(0);            
        //     $table->dateTime('tgl_dilayani')->nullable();
        //     $table->boolean('is_dilayani')->default(0);

        //     $table->boolean('is_rujukan_int')->default(0);

        //     $table->string('no_rujukan', 30)->nullable();
        //     $table->string('kode_kunjungan', 2)->nullable();
        //     $table->string('jenis_kunjungan', 50)->nullable();
        //     $table->tinyText('keterangan_batal')->nullable();
            
        //     $table->jsonb('json_request')->nullable();
        //     $table->jsonb('json_response')->nullable();            
        //     $table->boolean('is_aktif')->default(0);
        //     $table->string('created_by', 50);
        //     $table->string('updated_by', 50)->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();

        //     $table->primary(['client_id', 'antrian_id']);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_antrian');
    }
};
