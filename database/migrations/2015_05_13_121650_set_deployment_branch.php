<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use REBELinBLUE\Deployer\Deployment;

class SetDeploymentBranch extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->string('branch')->default('master');
        });

        foreach (Deployment::all() as $deployment) {
            $deployment->branch = $deployment->project->branch;
            $deployment->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn('branch');
        });
    }
}
