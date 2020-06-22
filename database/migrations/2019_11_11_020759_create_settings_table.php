<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 64)->unique();
            $table->string('value')->nullable();
            $table->string('label')->nullable();
            $table->string('group', 64);
            $table->string('display_group');
            $table->enum('input_type', [
                'checkbox','color', 'date', 'datetime','text', 'password', 
                'email', 'file', 'image', 'number', 'time', 'select'
            ]);
            $table->string('dataenum')->nullable();;    
            $table->string('helper')->nullable();;
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
