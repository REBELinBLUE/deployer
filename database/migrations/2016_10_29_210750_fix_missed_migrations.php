<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use REBELinBLUE\Deployer\Deployment;

class FixMissedMigrations extends Migration
{
    private $relations = ['Command', 'Variable', 'ConfigFile', 'SharedFile'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE server_logs MODIFY output longtext');
            DB::statement("ALTER TABLE deployments CHANGE status status ENUM('"
                . Deployment::PENDING . "', '"
                . Deployment::DEPLOYING . "', '"
                . Deployment::COMPLETED . "', '"
                . Deployment::FAILED . "', '"
                . Deployment::COMPLETED_WITH_ERRORS . "', '"
                . Deployment::ABORTING . "', '"
                . Deployment::ABORTED . "') DEFAULT '" . Deployment::PENDING . "'");
        }

        if (config('database.default') !== 'sqlite') {
            DB::statement("ALTER TABLE projects DROP COLUMN is_template");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE server_logs MODIFY output text');
            DB::statement("ALTER TABLE deployments CHANGE status status ENUM('"
                . Deployment::PENDING . "', '"
                . Deployment::DEPLOYING . "', '"
                . Deployment::COMPLETED . "', '"
                . Deployment::FAILED . "', '"
                . Deployment::COMPLETED_WITH_ERRORS . "') DEFAULT '" . Deployment::PENDING . "'");
        }

        if (config('database.default') !== 'sqlite') {
            Schema::table('projects', function (Blueprint $table) {
                $table->boolean('is_template')->default(false);
            });
        }
    }
}
