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
        Schema::connection('dbclient')->create('tb_penyesuaian', function (Blueprint $table) {
            $table->string('penyesuaian_id', 100)->primary();
            $table->string('jenis_penyesuaian', 50);
            $table->string('depo_id', 100);
            $table->string('depo_nama', 100)->nullable();
            $table->text('catatan');
            $table->dateTime('tgl_penyesuaian')->nullable();
            $table->dateTime('tgl_selesai')->nullable();
            $table->string('biaya_unit_id',100)->nullable();
            $table->string('status', 50);

            $table->string('approver', 100)->nullable();
            $table->string('approver_id', 100)->nullable();
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
        Schema::connection('dbclient')->dropIfExists('tb_penyesuaian');
    }
};
