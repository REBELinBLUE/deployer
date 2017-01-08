<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixTimestampDefaults extends Migration
{
    private $relations = ['CheckUrl', 'Command', 'ConfigFile', 'Deployment', 'DeployStep', 'Group', 'Heartbeat',
    'Notification', 'NotifyEmail', 'Project', 'Ref', 'ServerLog', 'Server', 'SharedFile', 'User', 'Template', 'Variable', ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('database.default') === 'mysql') {
            DB::statement("SET SESSION sql_mode='ALLOW_INVALID_DATES'");

            foreach ($this->relations as $relation) {
                $className = "REBELinBLUE\\Deployer\\$relation";
                $instance  = new $className;

                $table = $instance->getTable();

                if ($table === 'channels') {
                    $table = 'notifications';
                }

                DB::statement("ALTER TABLE {$table} MODIFY COLUMN created_at timestamp NULL DEFAULT NULL");
                DB::statement("ALTER TABLE {$table} MODIFY COLUMN updated_at timestamp NULL DEFAULT NULL");
            }

            DB::statement('ALTER TABLE failed_jobs MODIFY COLUMN failed_at timestamp NULL DEFAULT NULL');
            DB::statement('ALTER TABLE password_resets MODIFY COLUMN created_at timestamp NULL DEFAULT NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Don't really need a down function, this was changed in laravel 5.2
        $this->up();
    }
}
