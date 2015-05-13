<?php

use App\Deployment;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetDeploymentBranch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->string('branch')->default('master');
        });

        // FIXME: This isn't working correctly!
        foreach (Deployment::all() as $deployment) {
            $deployment->branch = $deployment->project->branch;
            $deployment->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn('branch');
        });
    }
}
