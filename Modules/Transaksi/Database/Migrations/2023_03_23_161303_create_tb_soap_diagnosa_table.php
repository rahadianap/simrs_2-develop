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
        Schema::connection('dbclient')->create('tb_soap_diagnosa', function (Blueprint $table) {
            $table->string('soap_diag_id', 100)->primary();
            $table->string('soap_id', 100);
            $table->string('reff_trx_id', 100)->nullable();
            $table->string('reg_id', 100)->nullable();
            $table->dateTime('tgl_diagnosa');

            $table->string('tipe',30)->nullable();
            $table->string('kode_icd',20)->nullable();
            $table->tinyText('nama_icd')->nullable();
            $table->tinyText('diagnosa')->nullable();
            $table->boolean('kasus_lama')->nullable();

            $table->string('pasien_id',100)->nullable();
            $table->string('no_rm',30)->nullable();
            $table->string('nama_pasien',100)->nullable();
            
            $table->string('dokter_id',100)->nullable();
            $table->string('dokter_nama',100)->nullable();
            
            $table->string('unit_id',100)->nullable();
            $table->string('unit_nama',100)->nullable();
                        
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
        Schema::connection('dbclient')->dropIfExists('tb_soap_diagnosa');
    }
};
