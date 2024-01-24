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
        Schema::connection('dbcentral')->create('tb_client', function (Blueprint $table) {
            $table->string('client_id', 100)->primary();
            $table->string('client_nama', 100);
            $table->string('client_tipe', 100)->nullable();
            $table->tinyText('deskripsi')->default('');
            $table->string('bpjs_cons_id', 100)->nullable();
            $table->string('bpjs_secret', 100)->nullable();
            $table->string('invitation_token', 100);
            $table->string('admin_id', 100);
            $table->string('admin_email', 100);   

            $table->tinyText('alamat')->default('');
            $table->string('propinsi', 100)->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kodepos', 100)->nullable();  
            $table->date('tgl_terbit_ijin')->nullable();
            $table->string('no_ijin', 50)->nullable();
            
            $table->string('no_telepon', 100)->nullable();  
            $table->string('no_whatsapp', 100)->nullable();  
            $table->string('link_instagram', 100)->nullable();  
            $table->string('email', 100)->nullable();  
                        
            $table->tinyText('path_logo')->nullable();
            $table->tinyText('url_logo')->nullable();
            $table->text('loc_embed_src')->nullable();
            $table->text('loc_embed_code')->nullable();      

            $table->boolean('is_aktif')->default(0);
            $table->string('created_by', 50);
            $table->string('updated_by', 50)->nullable();
            $table->string('deleted_by', 50)->nullable();
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
        Schema::connection('dbcentral')->dropIfExists('tb_client');
    }
};
