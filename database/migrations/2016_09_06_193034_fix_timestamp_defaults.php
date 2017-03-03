<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixTimestampDefaults extends Migration
{
    private $tables = ['notifications', 'notify_emails', 'check_urls', 'commands', 'config_files', 'deployments',
                       'deploy_steps', 'groups', 'heartbeats', 'projects', 'refs', 'server_logs', 'servers',
                       'shared_files', 'users', 'templates', 'variables', ];

    /**
     * Run the migrations.
     */
    public function up()
    {
        $connection = config('database.default');
        $driver     = config('database.connections.' . $connection . '.driver');
        if ($driver === 'mysql') {
            DB::statement("SET SESSION sql_mode='ALLOW_INVALID_DATES'");

            foreach ($this->tables as $table) {
                DB::statement("ALTER TABLE {$table} MODIFY COLUMN created_at timestamp NULL DEFAULT NULL");
                DB::statement("ALTER TABLE {$table} MODIFY COLUMN updated_at timestamp NULL DEFAULT NULL");
            }

            DB::statement('ALTER TABLE failed_jobs MODIFY COLUMN failed_at timestamp NULL DEFAULT NULL');
            DB::statement('ALTER TABLE password_resets MODIFY COLUMN created_at timestamp NULL DEFAULT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Don't really need a down function, this was changed in laravel 5.2
        $this->up();
    }
}
