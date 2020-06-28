<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('custom_id')->nullable();
            $table->string('title', 16)->nullable();
            $table->string('name', 64);
            $table->string('email', 64)->nullable();
            $table->string('mobile', 32)->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('fax', 32)->nullable();
            $table->string('tax_no', 64)->nullable();
            $table->string('address')->nullable();
            $table->string('company', 128)->nullable();

            $table->boolean('is_archive')->default(false);
            $table->boolean('is_customer')->default(false);
            $table->boolean('is_supplier')->default(false);
            $table->boolean('is_employee')->default(false);
            $table->boolean('is_others')->default(false);

            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
