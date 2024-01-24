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
        Schema::connection('dbclient')->create('tb_trx_rad_hasil', function (Blueprint $table) {
            $table->string('detail_id', 100);
            $table->string('trx_id', 100);
            $table->string('reg_id', 100);
            $table->string('tindakan_id', 100);
            $table->string('tindakan_nama', 100)->nullable();            
            $table->string('jenis_foto', 100)->nullable();
            $table->string('no_foto', 100)->nullable();
            $table->string('uraian_hasil', 100)->nullable();
            $table->string('kesan', 100)->nullable();
            $table->string('catatan', 100)->nullable();
            $table->date('tgl_hasil')->nullable();
            $table->string('dokter_id', 100)->nullable();
            $table->string('dokter_nama', 100)->nullable();
            $table->string('expertise_id', 100)->nullable();
            $table->string('expertise_nama', 100)->nullable();
            
            $table->string('status', 20)->nullable();

            $table->boolean('is_aktif');
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['detail_id', 'trx_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_rad_hasil');
    }
};
