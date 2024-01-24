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
        Schema::connection('dbclient')->create('tb_bhp', function (Blueprint $table) {
            $table->string('trx_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('reff_trx_id', 100)->nullable();
            $table->dateTime('tgl_transaksi');
            $table->string('jns_resep', 30);
            $table->dateTime('tgl_resep');
            $table->string('no_resep', 100)->nullable();
            
            $table->string('unit_id', 100)->nullable();
            $table->string('unit_nama', 100)->nullable();
            $table->string('depo_id', 100)->nullable();
            $table->string('depo_nama', 100)->nullable();
            $table->string('peresep', 100)->nullable();

            $table->string('jns_transaksi', 30);
            $table->string('no_antrian', 5)->nullable();
            $table->string('no_transaksi', 100)->nullable();
            
            $table->string('penjamin_id', 100)->nullable();
            $table->string('penjamin_nama', 100)->nullable();
            
            $table->string('dokter_id', 100)->nullable();
            $table->string('dokter_nama', 100)->nullable();

            $table->string('pasien_id', 100)->nullable();
            $table->string('nama_pasien', 100)->nullable();
            $table->string('no_rm', 10)->nullable();

            $table->decimal('total');

            $table->string('status', 100);
            $table->boolean('is_aktif');
            $table->boolean('is_realisasi')->default(0);
            $table->boolean('is_bayar')->default(0);
            $table->boolean('is_bhp')->default(0);
            
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
        Schema::connection('dbclient')->dropIfExists('tb_bhp');
    }
};
