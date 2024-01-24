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
        Schema::connection('dbclient')->create('tb_bed', function (Blueprint $table) {
            $table->string('bed_id', 100)->primary();
            $table->string('ruang_id', 100);
            $table->string('no_bed', 5)->nullable();
            $table->string('jns_kelamin_bed', 15)->nullable();
            $table->string('pasien_id', 100)->nullable();
            $table->string('nama_pasien', 100)->nullable();
            $table->string('no_rm', 12)->nullable();
            $table->string('reg_id', 100)->nullable();
            $table->string('trx_id', 100)->nullable();
            $table->date('tgl_masuk')->nullable();
            $table->time('jam_masuk')->nullable();

            $table->string('penjamin_id', 100)->nullable();
            $table->string('penjamin_nama', 100)->nullable();
            
            $table->string('status', 100)->nullable();
            $table->boolean('is_tersedia')->nullable();
            $table->dateTime('tgl_rencana_pulang')->nullable();

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
        Schema::connection('dbclient')->dropIfExists('tb_bed');
    }
};
