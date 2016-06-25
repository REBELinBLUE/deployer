<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeployStepsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('deploy_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('deployment_id');
            $table->unsignedInteger('stage');
            $table->unsignedInteger('command_id')->nullable();
            $table->timestamps();
            $table->foreign('deployment_id')->references('id')->on('deployments');
            $table->foreign('command_id')->references('id')->on('commands');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('deploy_steps');
    }
}
