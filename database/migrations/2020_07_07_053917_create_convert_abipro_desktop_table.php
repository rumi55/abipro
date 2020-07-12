<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConvertAbiproDesktopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convert_abipro_desktop', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('dbf_file');
            $table->string('dbf_type', 64);
            $table->string('target', 64);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('convert_abipro_desktop');
    }
}
