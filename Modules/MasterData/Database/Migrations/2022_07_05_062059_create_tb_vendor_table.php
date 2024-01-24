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
        Schema::connection('dbclient')->create('tb_vendor', function (Blueprint $table) {
            $table->string('vendor_id', 100)->primary();
            $table->string('vendor_nama', 100);
            $table->string('inisial', 100);
            $table->text('alamat');
            $table->string('npwp', 100)->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email',100)->nullable();
            $table->string('narahubung', 100)->nullable();
            $table->string('no_kontrak', 100)->nullable();
            $table->date('tgl_kontrak')->nullable();
            $table->date('tgl_akhir_kontrak')->nullable();
            $table->string('status', 50);
            $table->boolean('is_pabrikan')->default(1);
            $table->boolean('nama_vendor')->default(1);
            $table->boolean('is_aktif')->default(1);
            $table->string('client_id', 100);
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
        Schema::connection('dbclient')->dropIfExists('tb_vendor');
    }
};
