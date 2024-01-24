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
        Schema::connection('dbcentral')->create('users', function (Blueprint $table) {
            $table->string('user_id', 100)->primary();
            $table->string('email', 100)->unique();
            $table->string('username', 100)->unique();
            $table->string('password');
            $table->string('phone_no')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->rememberToken();
            $table->boolean('is_aktif')->default(0);
            $table->string('avatar', 200)->nullable();
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
        Schema::connection('dbcentral')->dropIfExists('users');
    }
    
};
