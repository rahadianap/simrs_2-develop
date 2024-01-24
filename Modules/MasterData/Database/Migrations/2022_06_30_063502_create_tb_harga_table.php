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
        Schema::connection('dbclient')->create('tb_harga', function (Blueprint $table) {
            $table->string('harga_id', 100)->unique();
            $table->string('tindakan_id', 100);
            $table->string('tindakan_nama', 100)->nullable();
            $table->string('kelas_id', 100);
            $table->string('kelas_nama', 100)->nullable();
            $table->string('buku_harga_id', 100);
            $table->string('buku_nama', 100)->nullable();
            $table->double('nilai', 18, 2)->nullable();
            $table->double('nilai_rencana', 18, 2);
            
            $table->boolean('is_approve')->default(0);
            $table->string('approver',100)->nullable();
            
            $table->string('status',20)->nullable();
            $table->boolean('is_aktif')->default(0);
            $table->string('client_id', 100);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['kelas_id', 'tindakan_id', 'buku_harga_id']);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_harga');
    }
};
