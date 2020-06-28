<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_no');
            $table->string('account_name');
            $table->boolean('has_children')->default(false);
            $table->string('type', 8)->nullable();
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('tree_level');
            $table->unsignedBigInteger('account_parent_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedInteger('account_type_id');
            
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            $table->foreign('account_type_id')->references('id')->on('account_types')->onDelete('cascade');
            $table->foreign('account_parent_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index(['account_no', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}