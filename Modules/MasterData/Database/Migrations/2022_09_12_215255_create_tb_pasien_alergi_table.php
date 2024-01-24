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
        Schema::connection('dbclient')->create('tb_pasien_alergi', function (Blueprint $table) {
            $table->string('pasien_alergi_id', 100)->primary();
            $table->string('pasien_id', 100)->nullable();
            $table->string('jns_alergi',100)->nullable();
            $table->date('tgl_kejadian_awal',100)->nullable();
            $table->text('akibat')->nullable();
            $table->text('catatan_alergi')->nullable();
            $table->boolean('is_aktif')->default(0);
            $table->string('client_id', 100);
            $table->string('created_by', 100);
            $table->string('updated_by', 100)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_pasien_alergi');
    }
};