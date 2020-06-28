<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNumberingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('numberings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('format');
            $table->enum('counter_reset', ['d', 'm', 'y', 'n'])->default('y');;
            $table->tinyInteger('counter_digit')->default(4);
            $table->tinyInteger('counter_start')->default(1);
            $table->string('transaction_type_id', 8)->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            $table->foreign('transaction_type_id')->references('id')->on('transaction_types')->onDelete('cascade');
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
        Schema::dropIfExists('numberings');
    }
}
