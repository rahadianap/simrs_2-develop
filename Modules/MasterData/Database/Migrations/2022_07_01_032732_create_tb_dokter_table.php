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
        Schema::connection('dbclient')->create('tb_dokter', function (Blueprint $table) {
            $table->string('dokter_id', 100)->primary();
            $table->string('dokter_nama', 100);
            $table->string('spesialis_id', 100)->nullable();
            $table->string('pend_akhir', 100)->nullable();
            $table->string('smf_id', 100)->nullable();
            $table->text('biografi')->nullable();
            $table->string('url_avatar', 100)->nullable();
            $table->string('no_registrasi', 100)->nullable();
            $table->string('no_sip', 100)->nullable();
            $table->string('tgl_akhir_sip', 100)->nullable();
            $table->string('status_kerjasama', 100)->nullable();
            $table->string('npwp', 20)->nullable();
            $table->string('rekening', 30)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('jns_kelamin', 2)->nullable();
            $table->string('no_telepon', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->string('user_id', 100)->nullable();
            $table->string('status', 100)->nullable();
            
            $table->boolean('is_aktif')->default(0);
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
        Schema::connection('dbclient')->dropIfExists('tb_dokter');
    }
};
