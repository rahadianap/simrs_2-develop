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
        Schema::connection('dbclient')->create('tb_template_gizi', function (Blueprint $table) {
            $table->string('temp_gizi_id', 100)->primary();
            $table->string('nama_template', 100);
            $table->date('tgl_dibuat');
            $table->date('tgl_berlaku');
            $table->tinyText('catatan')->nullable();
            $table->double('jml_hari', 18, 0);
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 100);
            $table->string('updated_by', 100);
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
        Schema::dropIfExists('tb_template_gizi');
    }
};
