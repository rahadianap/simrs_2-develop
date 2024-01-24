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
        Schema::connection('dbclient')->create('tb_penjamin', function (Blueprint $table) {
            $table->string('penjamin_id', 100)->primary();
            $table->string('jns_penjamin', 100);
            $table->string('penjamin_nama', 100);
            $table->string('inisial', 50);
            $table->string('npwp', 30);
            $table->string('jenis_biaya_admin', 30);
            $table->string('narahubung', 100);
            $table->string('no_telp', 20);
            $table->string('email', 100)->nullable();
            $table->text('alamat');
            $table->string('buku_harga_id', 100);
            $table->string('buku_harga', 100);
            
            $table->double('margin_resep_poli', 18, 2)->nullable();
            $table->double('margin_resep_inap', 18, 2)->nullable();
            $table->double('margin_bhp', 18, 2)->nullable();

            $table->string('jenis_biaya_admin',50)->nullable();
            $table->boolean('is_fix_admin')->default(0);
            $table->double('nilai_admin', 18, 2)->nullable();
            $table->double('nilai_maks_admin', 18, 2)->nullable();
            
            $table->boolean('is_coverage_poli')->default(0);
            $table->boolean('is_coverage_inap')->default(0);
            $table->boolean('is_coverage_penunjang')->default(0);
            
            $table->string('coa_proses',100)->nullable();
            $table->string('coa_invoice')->nullable();
            $table->string('coa_deposit')->nullable();

            $table->string('coa_proses_id',100)->nullable();
            $table->string('coa_invoice_id',100)->nullable();
            $table->string('coa_deposit_id',100)->nullable();
            
            $table->boolean('is_bpjs')->default(0);
            $table->boolean('is_aktif')->default(0);
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
        Schema::dropIfExists('tb_penjamin');
    }
};
