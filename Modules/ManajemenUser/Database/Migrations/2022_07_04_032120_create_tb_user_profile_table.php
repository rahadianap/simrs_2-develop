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
        Schema::connection('dbcentral')->create('tb_user_profile', function (Blueprint $table) {
            $table->string('user_id', 100)->primary();
            $table->string('nama_lengkap', 100)->nullable();
            $table->string('jenis_kelamin', 100)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->tinyText('instagram')->nullable();
            $table->string('nik', 20)->nullable();
            $table->tinyText('avatar_path')->nullable();
            $table->tinyText('avatar_url')->nullable();
            $table->tinyText('bio_singkat')->nullable();            
            $table->boolean('is_aktif')->default(0);
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
        Schema::connection('dbcentral')->dropIfExists('tb_user_profile');
    }
};
