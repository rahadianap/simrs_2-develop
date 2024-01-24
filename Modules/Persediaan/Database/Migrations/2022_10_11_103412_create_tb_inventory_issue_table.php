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
        Schema::connection('dbclient')->create('tb_inven_issue', function (Blueprint $table) {
            $table->string('inven_issue_id', 100)->primary();
            $table->dateTime('tgl_dibuat');
            $table->dateTime('tgl_issue')->nullable();
            $table->dateTime('tgl_selesai')->nullable();
            $table->text('catatan');
            $table->string('depo_id', 100);
            $table->string('depo_nama', 100);
            $table->string('unit_id', 100);
            $table->string('approver_id', 100);
            $table->string('approver', 100);
            $table->string('status', 50);

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
        Schema::connection('dbclient')->dropIfExists('tb_inven_issue');
    }
};
