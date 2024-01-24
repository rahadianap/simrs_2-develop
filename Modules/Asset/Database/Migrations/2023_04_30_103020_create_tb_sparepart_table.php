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
        Schema::connection('dbclient')->create('tb_sparepart', function (Blueprint $table) {
            $table->string('sparepart_id', 100)->primary();
            $table->string('sparepart_nama', 100);
            $table->string('serial_no', 100);
            $table->string('brand_nama', 100);
            $table->tinyText('deskripsi');
            $table->date('tgl_kadaluarsa');
            $table->string('status', 10);

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
        Schema::dropIfExists('tb_sparepart');
    }
};
