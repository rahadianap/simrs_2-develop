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
        Schema::connection('dbclient')->create('tb_penjamin_log', function (Blueprint $table) {
            $table->string('penjamin_log_id', 100)->primary();
            $table->string('reg_id', 100);
            $table->string('trx_id', 100);
            $table->date('tgl_ubah');
            $table->string('penjamin_id', 100);
            $table->string('penjamin_nama', 100)->nullable();
            $table->string('kelas', 100)->nullable();

            $table->string('penjamin_asal_id', 100)->nullable();
            $table->string('penjamin_asal', 100)->nullable();
            $table->string('kelas_asal', 100)->nullable();

            $table->string('trx_status',20);
            $table->boolean('is_aktif');
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
        Schema::connection('dbclient')->dropIfExists('tb_penjamin_log');
    }
};
