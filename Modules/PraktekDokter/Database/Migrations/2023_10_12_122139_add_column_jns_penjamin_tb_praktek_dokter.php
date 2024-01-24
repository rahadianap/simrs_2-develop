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
        Schema::connection('dbclient')->table('tb_praktek_dokter', function (Blueprint $table) {
            $table->string('jns_penjamin',50)->nullable();
            $table->string('tempat_lahir',100)->nullable();
            $table->string('no_antrian',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->table('tb_praktek_dokter', function (Blueprint $table) {

        });
    }
};
