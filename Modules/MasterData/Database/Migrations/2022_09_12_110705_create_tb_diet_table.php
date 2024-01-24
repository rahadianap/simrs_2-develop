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
        Schema::connection('dbclient')->create('tb_diet', function (Blueprint $table) {
            $table->string('diet_id', 100)->primary();
            $table->string('nama_diet', 100);
            $table->tinyText('catatan')->nullable();
            $table->tinyText('komplikasi');
            $table->boolean('is_semua_kelas');
            $table->float('nilai_kalori');
            $table->float('int_kalori');
            $table->float('min_kalori');
            $table->float('max_kalori');
            $table->float('nilai_karbo');
            $table->float('int_karbo');
            $table->float('min_karbo');
            $table->float('max_karbo');
            $table->float('nilai_protein');
            $table->float('int_protein');
            $table->float('min_protein');
            $table->float('max_protein');
            $table->float('nilai_lemak');
            $table->float('int_lemak');
            $table->float('min_lemak');
            $table->float('max_lemak');
            $table->float('nilai_serat');
            $table->float('int_serat');
            $table->float('min_serat');
            $table->float('max_serat');
            $table->float('nilai_garam');
            $table->float('int_garam');
            $table->float('min_garam');
            $table->float('max_garam');
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
        Schema::dropIfExists('tb_diet');
    }
};
