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
        Schema::connection('dbclient')->create('tb_role_menu', function (Blueprint $table) {
            $table->string('role_id', 100);
            $table->string('client_id', 100);
            $table->string('menu_id', 100);
            $table->jsonb('otorisasi')->nullable();
            $table->boolean('is_aktif');            
            $table->boolean('is_admin_mandatory')->default(0);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['role_id', 'client_id', 'menu_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_role_menu');
    }
};
