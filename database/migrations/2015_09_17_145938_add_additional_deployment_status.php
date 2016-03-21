<?php

use Illuminate\Database\Migrations\Migration;
use REBELinBLUE\Deployer\Deployment;

class AddAdditionalDeploymentStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (isset($_ENV['DB_TYPE']) && $_ENV['DB_TYPE'] === 'mysql') {
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
     *
     * @return void
     */
    public function down()
    {
        if (isset($_ENV['DB_TYPE']) && $_ENV['DB_TYPE'] === 'mysql') {
            DB::statement("ALTER TABLE deployments CHANGE status status ENUM('"
                . Deployment::PENDING . "', '"
                . Deployment::DEPLOYING . "', '"
                . Deployment::COMPLETED . "', '"
                . Deployment::FAILED . "') DEFAULT '" . Deployment::PENDING . "'");
        }
    }
}
