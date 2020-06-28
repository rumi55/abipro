<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 64);
            $table->string('email', 64);
            $table->string('photo')->nullable();
            $table->string('phone', 16)->nullable();
            $table->string('password');
            $table->string('activation_token')->nullable();
            $table->boolean('is_owner')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->rememberToken();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
