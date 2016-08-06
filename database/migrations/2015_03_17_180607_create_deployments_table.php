<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use REBELinBLUE\Deployer\Deployment;

class CreateDeploymentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('deployments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('committer');
            $table->string('commit');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('user_id')->nullable()->default(null);
            $table->enum('status', [Deployment::PENDING, Deployment::DEPLOYING,
                                    Deployment::COMPLETED, Deployment::FAILED,
                                    Deployment::COMPLETED_WITH_ERRORS,
                                    Deployment::ABORTING, Deployment::ABORTED, ])->default(Deployment::PENDING);
            $table->timestamps();
            $table->softDeletes();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('deployments');
    }
}
