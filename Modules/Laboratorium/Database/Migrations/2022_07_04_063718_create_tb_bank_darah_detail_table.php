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
        Schema::connection('dbclient')->create('tb_bank_darah_detail', function (Blueprint $table) {
            $table->string('detail_id', 100);
            $table->string('reg_id', 100);
            $table->string('trx_id', 100);
            $table->string('reff_trx_id', 100)->nullable();
            $table->string('jenis_permintaan', 100);
            $table->string('tindakan_nama', 100);
            $table->string('gol_darah', 2)->nullable();
            $table->integer('jumlah');
            $table->string('volume', 100)->nullable();
            $table->string('satuan', 100)->nullable();
            $table->string('no_labu', 100)->nullable();
            $table->string('hasil_crossmatch', 100)->nullable();
            $table->string('dokter_id', 100);

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['detail_id', 'trx_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_bank_darah_detail');
    }
};
