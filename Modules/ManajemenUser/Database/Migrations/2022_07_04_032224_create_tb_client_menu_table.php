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
        Schema::connection('dbcentral')->create('tb_client_menu', function (Blueprint $table) {
            $table->string('menu_id', 100);
            $table->string('client_id', 100);
            $table->string('group_menu', 100);
            $table->string('menu_title', 100);
            $table->string('no_urut', 5)->nullable();
            $table->boolean('is_nav_header')->default(0);
            $table->string('menu_icon', 20)->nullable();
            $table->jsonb('otorisasi')->nullable();
            $table->string('menu_link', 200)->nullable();            
            $table->boolean('is_aktif');
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['menu_id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbcentral')->dropIfExists('tb_client_menu');
    }
};
