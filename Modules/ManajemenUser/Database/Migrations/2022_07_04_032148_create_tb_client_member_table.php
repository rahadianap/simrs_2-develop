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
        Schema::connection('dbcentral')->create('tb_client_member', function (Blueprint $table) {
            $table->string('user_id', 100);
            $table->string('client_id', 100);
            $table->string('role_id', 100)->nullable();
            $table->string('tipe_member', 100)->nullable();
            $table->dateTime('tgl_gabung')->nullable();
            $table->boolean('is_admin')->default(0);
            $table->boolean('is_undang_gabung')->default(0);                        
            $table->boolean('is_aktif');
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['user_id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbcentral')->dropIfExists('tb_client_member');
    }
};
