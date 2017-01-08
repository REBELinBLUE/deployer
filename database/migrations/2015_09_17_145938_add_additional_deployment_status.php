<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use REBELinBLUE\Deployer\Deployment;

class AddAdditionalDeploymentStatus extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE deployments CHANGE status status ENUM('"
                . Deployment::PENDING . "', '"
                . Deployment::DEPLOYING . "', '"
                . Deployment::COMPLETED . "', '"
                . Deployment::FAILED . "', '"
                . Deployment::COMPLETED_WITH_ERRORS . "') DEFAULT '" . Deployment::PENDING . "'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE deployments CHANGE status status ENUM('"
                . Deployment::PENDING . "', '"
                . Deployment::DEPLOYING . "', '"
                . Deployment::COMPLETED . "', '"
                . Deployment::FAILED . "') DEFAULT '" . Deployment::PENDING . "'");
        }
    }
}
