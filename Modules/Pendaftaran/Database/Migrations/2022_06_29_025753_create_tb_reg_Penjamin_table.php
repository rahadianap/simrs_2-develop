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
        Schema::connection('dbclient')->create('tb_reg_penjamin', function (Blueprint $table) {
            $table->string('reg_id', 100)->primary();
            $table->string('jns_penjamin', 100);
            $table->string('penjamin_id', 100);
            $table->string('penjamin_nama', 100);
            $table->string('no_kepesertaan', 100)->nullable();

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
        Schema::connection('dbclient')->dropIfExists('tb_reg_Penjamin');
    }
};
