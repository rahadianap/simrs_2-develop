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
        Schema::connection('dbclient')->create('tb_pa_detail', function (Blueprint $table) {
            $table->string('detail_id', 100);
            $table->string('trx_id', 100);
            $table->string('reg_id', 100);
            $table->string('reff_trx_id', 100)->nullable();
            $table->string('tindakan_id', 100);
            $table->string('tindakan_nama', 100);
            $table->tinyInteger('jumlah');
            $table->string('satuan', 100)->nullable();
            $table->string('dokter_id', 100)->nullable();
            $table->string('status', 100)->nullable();

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['detail_id','trx_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_pa_detail');
    }
};
