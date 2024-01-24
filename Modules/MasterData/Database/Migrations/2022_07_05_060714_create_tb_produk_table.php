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
        Schema::connection('dbclient')->create('tb_produk', function (Blueprint $table) {
            $table->string('produk_id', 100)->primary();
            $table->string('produk_nama', 100);
            $table->string('barcode', 100)->nullable();
            $table->string('jenis_produk', 100);
            $table->string('satuan_beli', 100)->nullable();
            $table->string('satuan', 100)->nullable();

            $table->double('konversi',18,2)->default(0);            
            $table->double('harga_beli',18,2)->default(0);
            $table->double('hna',18,2)->default(0);
            $table->double('het',18,2)->default(0);

            $table->boolean('is_hasil_produksi')->default(0);
            $table->boolean('is_jual')->default(0);
            $table->boolean('is_pengadaan')->default(0);
            $table->boolean('is_sterilisasi')->default(0);
            $table->boolean('is_kontrol_kadaluarsa')->default(0);
            $table->boolean('is_kontrol_stok')->default(0);
            $table->boolean('is_laundry_item')->default(0);
            
            $table->string('cara_pakai', 100)->nullable();
            $table->string('signa', 100)->nullable();
            $table->string('aturan_pakai', 100)->nullable();
            $table->string('label_obat', 100)->nullable();
            $table->string('sediaan', 100)->nullable();
            $table->jsonb('klasifikasi')->nullable();
            $table->jsonb('golongan')->nullable();
            $table->text('komposisi', 100)->nullable();
            $table->text('kontra_indikasi', 100)->nullable();
            $table->string('vendor_id', 100)->nullable();
            $table->string('vendor', 100)->nullable();            
            $table->string('pabrikan_id', 100)->nullable();
            $table->string('pabrikan', 100)->nullable();      
            $table->text('meta_data')->nullable();      
            $table->string('produk_akun', 100)->nullable();            
            $table->string('produk_akun_id', 100)->nullable();            
            $table->string('kelompok_vclaim_id',100)->nullable();
            $table->string('kelompok_vclaim',100)->nullable();
            $table->string('kelompok_tagihan_id',100)->nullable();
            $table->string('kelompok_tagihan',100)->nullable();
            $table->string('jenis_etiket',100)->nullable();
            $table->boolean('is_aktif');
            $table->string('client_id', 100);
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
        Schema::connection('dbclient')->dropIfExists('tb_produk');
    }
};
