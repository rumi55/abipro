<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('counters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('counter');
            $table->string('period')->nullable();//y for yearly,
            $table->string('last_number')->nullable();
            $table->unsignedBigInteger('numbering_id');
            $table->unsignedBigInteger('company_id');
            $table->timestampsTz();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('numbering_id')->references('id')->on('numberings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('counters');
    }
}
