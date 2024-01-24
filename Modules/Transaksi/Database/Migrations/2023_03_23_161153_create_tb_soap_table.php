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
        Schema::connection('dbclient')->create('tb_soap', function (Blueprint $table) {
            $table->string('soap_id', 100)->primary();
            $table->string('reff_trx_id', 100);
            $table->string('reg_id', 100)->nullable();
            $table->dateTime('tgl_soap');
            
            $table->string('unit_id', 100);
            $table->string('unit_nama', 100)->nullable();
            $table->string('dokter_id', 100);
            $table->string('dokter_nama', 100)->nullable();

            $table->string('pasien_id', 100)->nullable();
            $table->string('nama_pasien', 100)->nullable();
            $table->string('no_rm', 10)->nullable();

            $table->double('saturasi_oksigen')->nullable();
            $table->double('sistole')->nullable();
            $table->double('diastole')->nullable();
            $table->double('suhu')->nullable();
            $table->double('denyut_nadi')->nullable();
            $table->double('pernapasan')->nullable();

            $table->longText('subjective')->nullable();
            $table->longText('objective')->nullable();
            $table->longText('assesment')->nullable();
            $table->longText('plan')->nullable();
            $table->longText('catatan')->nullable();
            
            $table->string('status', 100);
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
        Schema::connection('dbclient')->dropIfExists('tb_soap');
    }
};
