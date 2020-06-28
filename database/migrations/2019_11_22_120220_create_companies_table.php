<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 128);
            $table->string('company_type_id', 16)->nullable();
            $table->string('address')->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('phone', 64)->nullable();
            $table->string('fax', 64)->nullable();
            $table->string('email', 64)->nullable();
            $table->string('website', 64)->nullable();
            $table->string('logo')->nullable();
            $table->string('tax_no', 64)->nullable();
            $table->unsignedTinyInteger('currency_id')->nullable();
            $table->date('accounting_start_date')->nullable();
            $table->tinyInteger('accounting_period')->nullable();
            $table->unsignedBigInteger('owner_id');
            $table->boolean('is_active')->default(false);
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
            $table->foreign('company_type_id')->references('id')->on('company_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
