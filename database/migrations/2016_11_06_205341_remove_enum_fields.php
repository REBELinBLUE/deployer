<?php

use Illuminate\Database\Migrations\Migration;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\ServerLog;

class RemoveEnumFields extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $connection = config('database.default');
        $driver     = config('database.connections.' . $connection . '.driver');
        if ($driver === 'mysql') {
            DB::transaction(function () {
                $this->removeEnum('projects', 'status', Project::NOT_DEPLOYED);
                $this->removeEnum('servers', 'status', Server::UNTESTED);
                $this->removeEnum('deployments', 'status', Deployment::PENDING);
                $this->removeEnum('commands', 'step', Command::AFTER_INSTALL);
                $this->removeEnum('server_logs', 'status', ServerLog::PENDING);
                $this->removeEnum('heartbeats', 'status', Heartbeat::UNTESTED);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Don't do anything
    }

    private function removeEnum($table, $column, $default)
    {
        DB::statement("ALTER TABLE {$table} ADD COLUMN tmp INT");
        DB::statement("UPDATE {$table} SET tmp = CAST(CAST({$column} AS CHAR) AS SIGNED)");
        DB::statement("ALTER TABLE {$table} DROP COLUMN {$column}");
        DB::statement("ALTER TABLE {$table} CHANGE COLUMN tmp {$column} INT(10) NOT NULL DEFAULT {$default}");
    }
}
