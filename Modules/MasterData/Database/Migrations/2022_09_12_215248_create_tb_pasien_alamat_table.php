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
        Schema::connection('dbclient')->create('tb_pasien_alamat', function (Blueprint $table) {
            $table->string('pasien_id', 100);
            $table->tinyText('alamat')->nullable();
            $table->string('rt',4)->nullable();
            $table->string('rw',4)->nullable();
            $table->string('propinsi_id')->nullable();
            $table->string('propinsi')->nullable();
            $table->string('kota_id')->nullable();
            $table->string('kota')->nullable();
            $table->string('kecamatan_id')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan_id',100)->nullable();
            $table->string('kelurahan',100)->nullable();
            $table->string('kodepos',100)->nullable();
            $table->boolean('is_ktp_sama_dgn_tinggal')->default(0);
            
            $table->tinyText('alamat_tinggal')->nullable();
            $table->string('rt_tinggal',4)->nullable();
            $table->string('rw_tinggal',4)->nullable();
            $table->string('propinsi_tinggal_id')->nullable();
            $table->string('propinsi_tinggal')->nullable();
            $table->string('kota_tinggal_id')->nullable();
            $table->string('kota_tinggal')->nullable();
            $table->string('kecamatan_tinggal_id')->nullable();
            $table->string('kecamatan_tinggal')->nullable();
            $table->string('kelurahan_tinggal_id',100)->nullable();
            $table->string('kelurahan_tinggal',100)->nullable();
            $table->string('kodepos_tinggal',100)->nullable();
            
            $table->boolean('is_aktif')->default(0);
            $table->string('client_id', 100);
            $table->string('created_by', 100);
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['pasien_id','client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_pasien_alamat');
    }
};
