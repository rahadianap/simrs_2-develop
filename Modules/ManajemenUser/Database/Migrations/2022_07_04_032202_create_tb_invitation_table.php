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
        Schema::connection('dbcentral')->create('tb_invitation', function (Blueprint $table) {
            $table->string('invitation_id', 100)->primary();
            $table->string('user_id', 100);
            $table->string('username', 100);
            $table->string('client_id', 100);
            $table->string('email', 100);            
            $table->string('invitation_token', 100);
            $table->dateTime('expired_at');
            $table->dateTime('response_at')->nullable();
            $table->string('dibuat_oleh', 100);
            $table->string('status', 30);
            $table->dateTime('tgl_dibuat');
            $table->boolean('is_aktif');
            $table->string('created_by', 50);
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
        Schema::connection('dbcentral')->dropIfExists('tb_invitation');
    }
};
