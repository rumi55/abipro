<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserGroupFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_group_features', function (Blueprint $table) {
            $table->unsignedBigInteger('user_group_id');
            $table->unsignedBigInteger('feature_id');
            $table->primary(['user_group_id', 'feature_id']);
            $table->foreign('user_group_id')->references('id')->on('user_groups')->onDelete('cascade');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_group_features');
    }
}
