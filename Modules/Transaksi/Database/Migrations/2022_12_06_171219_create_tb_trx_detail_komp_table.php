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
        Schema::connection('dbclient')->create('tb_trx_detail_komp', function (Blueprint $table) {
            $table->string('komp_detail_id', 100);
            $table->string('detail_id', 100);
            $table->string('trx_id', 100);
            $table->string('reg_id', 100);
            
            $table->string('harga_id', 100);
            $table->string('komp_harga_id', 100);
            $table->string('komp_harga', 100);
            
            $table->double('jumlah', 18, 2);
            $table->double('nilai', 18, 2);
            $table->double('diskon', 18, 2);
            $table->double('nilai_diskon', 18, 2);
            $table->double('subtotal', 18, 2);

            $table->string('dokter_id', 100)->nullable();
            $table->string('dokter_nama', 100)->nullable();
            $table->tinyText('deskripsi')->nullable();
            $table->boolean('is_realisasi');
            $table->boolean('is_bayar');
            $table->boolean('is_aktif');
            $table->boolean('is_ubah_manual')->default(0);
            $table->boolean('is_diskon')->default(0);
            
            $table->string('client_id', 100);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['trx_id', 'detail_id', 'komp_detail_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbclient')->dropIfExists('tb_trx_detail_komp');
    }
};
