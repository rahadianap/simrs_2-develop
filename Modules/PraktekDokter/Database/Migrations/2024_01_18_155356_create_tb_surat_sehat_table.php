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
        Schema::connection('dbclient')->create('tb_surat_sehat', function (Blueprint $table) {
            $table->string('reg_id', 100);
            $table->date('tgl_dibuat');
            
            $table->string('nama_pasien',100)->nullable();
            $table->string('pasien_id',100)->nullable();
            $table->string('jns_kelamin',2)->nullable();
            $table->string('no_rm',100)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->tinyInteger('umur')->nullable();
            $table->text('alamat')->nullable();

            $table->double('sistole')->nullable();
            $table->double('diastole')->nullable();
            $table->double('suhu')->nullable();
            $table->double('denyut_nadi')->nullable();
            $table->double('pernapasan')->nullable();
            $table->double('berat_badan')->nullable();
            $table->double('tinggi_badan')->nullable();

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
        Schema::connection('dbclient')->dropIfExists('tb_surat_sehat');
    }
};
