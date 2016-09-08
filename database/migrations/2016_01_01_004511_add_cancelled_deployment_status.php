<?php

use Illuminate\Database\Migrations\Migration;
use REBELinBLUE\Deployer\Deployment;

class AddCancelledDeploymentStatus extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (isset($_ENV['DB_CONNECTION']) && $_ENV['DB_CONNECTION'] === 'mysql') {
            DB::statement("ALTER TABLE deployments CHANGE status status ENUM('"
                . Deployment::PENDING . "', '"
                . Deployment::DEPLOYING . "', '"
                . Deployment::COMPLETED . "', '"
                . Deployment::FAILED . "', '"
                . Deployment::COMPLETED_WITH_ERRORS . "', '"
                . Deployment::ABORTING . "', '"
                . Deployment::ABORTED . "') DEFAULT '" . Deployment::PENDING . "'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (isset($_ENV['DB_CONNECTION']) && $_ENV['DB_CONNECTION'] === 'mysql') {
            DB::statement("ALTER TABLE deployments CHANGE status status ENUM('"
                . Deployment::PENDING . "', '"
                . Deployment::DEPLOYING . "', '"
                . Deployment::COMPLETED . "', '"
                . Deployment::FAILED . "', '"
                . Deployment::COMPLETED_WITH_ERRORS . "') DEFAULT '" . Deployment::PENDING . "'");
        }
    }
}
