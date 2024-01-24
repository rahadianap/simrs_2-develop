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
        Schema::connection('dbclient')->create('tb_sep', function (Blueprint $table) {
            $table->string('reg_id', 100)->primary();
            $table->string('no_sep', 100);
            $table->date('tgl_sep');
            $table->string('pasien_id', 100);
            $table->string('no_rm', 100);
            $table->string('no_kepesertaan', 100);
            $table->text('diag_awal');
            $table->text('catatan');
            $table->string('sep_request', 100);
            $table->string('sep_response', 100);

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
        Schema::connection('dbclient')->dropIfExists('tb_sep');
    }
};
