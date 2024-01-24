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
        Schema::connection('dbclient')->create('tb_harga_detail', function (Blueprint $table) {
            $table->string('harga_id', 100);
            $table->string('komp_harga_id', 100);
            $table->string('komp_harga', 100);
            
            $table->double('nilai', 18, 2)->nullable();
            $table->double('nilai_rencana', 18, 2);
            
            $table->boolean('is_diskon')->default(0);
            $table->boolean('is_ubah_manual')->default(0);

            $table->boolean('is_aktif')->default(0);
            $table->string('status', 20)->nullable();
            $table->string('client_id', 100);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['harga_id', 'komp_harga_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_harga_detail');
    }
};
