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
        Schema::connection('dbclient')->create('tb_surat_sakit', function (Blueprint $table) {
            $table->string('reg_id', 100);
            $table->string('soap_id', 100);
            $table->json('soap_diag_id');
            $table->date('tgl_dibuat');
            
            $table->string('nama_pasien',100)->nullable();
            $table->string('pasien_id',100)->nullable();
            $table->string('no_rm',100)->nullable();
            $table->tinyInteger('umur')->nullable();
            $table->string('pekerjaan',100)->nullable();
            $table->text('alamat')->nullable();
            
            $table->string('kateg_istirahat',100)->nullable();
            $table->tinyInteger('hari')->nullable();
            $table->date('tgl_awal')->nullable();
            $table->date('tgl_akhir')->nullable();
            $table->text('catatan')->nullable();

            $table->string('dokter_id',100)->nullable();
            $table->string('dokter_nama',100)->nullable();

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['reg_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_surat_sakit');
    }
};
