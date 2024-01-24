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
        Schema::connection('dbclient')->table('tb_soap', function (Blueprint $table) {
            $table->double('berat_badan')->nullable();
            $table->double('tinggi_badan')->nullable();
            $table->longText('note_assesmen')->nullable();
            $table->longText('intervention')->nullable();
            $table->longText('evaluation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->table('tb_soap', function (Blueprint $table) {

        });
    }
};
