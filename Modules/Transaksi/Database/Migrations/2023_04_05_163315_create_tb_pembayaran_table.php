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
        Schema::connection('dbclient')->create('tb_pembayaran', function (Blueprint $table) {
            $table->string('payment_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('interim_id', 100);
            $table->string('tgl_bayar', 100);
            $table->string('jns_payment', 100);
            $table->double('jml_bayar', 18,2);
            $table->string('jenis_kartu', 20)->nullable();
            $table->string('penjamin_id', 100)->nullable();
            $table->string('penjamin_nama', 100)->nullable();
            $table->string('mesin_edc', 50)->nullable();
            $table->string('no_kartu', 50)->nullable();
            $table->boolean('is_aktif')->default(0);
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
        Schema::connection('dbclient')->dropIfExists('tb_pembayaran');
    }
};
