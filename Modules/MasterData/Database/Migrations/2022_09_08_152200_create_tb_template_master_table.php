<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('dbcentral')->create('tb_template_master', function (Blueprint $table) {
            $table->string('template_id', 100)->primary();
            $table->string('template_nama', 100);
            $table->string('deskripsi', 100)->nullable();

            $table->boolean('is_aktif')->default(0);
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('tb_template_master');
    }
};
