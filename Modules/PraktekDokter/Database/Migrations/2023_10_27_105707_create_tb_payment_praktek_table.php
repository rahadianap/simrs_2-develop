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
        Schema::connection('dbclient')->create('tb_payment_praktek', function (Blueprint $table) {
            $table->string('reg_id', 100);
            $table->string('interim_id', 100);
            $table->date('tgl_periksa');
            $table->dateTime('tgl_bayar');
            
            $table->string('nama_pasien',100)->nullable();
            $table->string('pasien_id',100)->nullable();
            $table->string('no_rm',100)->nullable();
            $table->string('jns_kelamin',2)->nullable();

            $table->string('dokter_id',100)->nullable();
            $table->string('dokter_nama',100)->nullable();

            $table->double('total_tagihan', 18,2)->nullable();
            $table->double('diskon', 18,2)->nullable();
            $table->double('total_akhir', 18,2)->nullable();
            $table->double('jml_bayar', 18,2)->nullable();
            $table->double('kembalian', 18,2)->nullable();

            $table->tinyText('alasan_batal')->nullable();
            $table->string('status', 20)->nullable();
            
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['reg_id', 'interim_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_payment_praktek');
    }
};
