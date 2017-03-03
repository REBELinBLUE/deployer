<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use REBELinBLUE\Deployer\Deployment;

class FixMissedMigrations extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $connection = config('database.default');
        $driver     = config('database.connections.' . $connection . '.driver');
        if ($driver === 'mysql') {
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

        if ($driver !== 'sqlite') {
            DB::statement('ALTER TABLE projects DROP COLUMN is_template');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $connection = config('database.default');
        $driver     = config('database.connections.' . $connection . '.driver');
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE server_logs MODIFY output text');
            DB::statement("ALTER TABLE deployments CHANGE status status ENUM('"
                . Deployment::PENDING . "', '"
                . Deployment::DEPLOYING . "', '"
                . Deployment::COMPLETED . "', '"
                . Deployment::FAILED . "', '"
                . Deployment::COMPLETED_WITH_ERRORS . "') DEFAULT '" . Deployment::PENDING . "'");
        }

        if ($driver !== 'sqlite') {
            Schema::table('projects', function (Blueprint $table) {
                $table->boolean('is_template')->default(false);
            });
        }
    }
}
