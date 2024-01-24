<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The database schema.
     *
     * @var \Illuminate\Database\Schema\Builder
     */
    protected $schema;

    /**
     * Create a new migration instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->schema = Schema::connection(env('DB_CONNECTION', 'mysql'));
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbcentral')->create('tb_user_reg', function (Blueprint $table) {
            $table->string('reg_id', 100)->primary();
            $table->string('email', 100);
            $table->string('username', 100);
            $table->string('password');
            $table->string('token', 20);
            $table->boolean('is_verified')->default(0);
            $table->dateTime('expired_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->string('status',15)->nullable();
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
        //$this->schema->dropIfExists('tb_user_reg');
        Schema::connection('dbcentral')->dropIfExists('tb_user_reg');
    }
};
