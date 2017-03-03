<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class RenameProjectFilesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $connection = config('database.default');
        $driver     = config('database.connections.' . $connection . '.driver');
        if ($driver === 'mysql') {
            DB::statement("SET SESSION sql_mode='ALLOW_INVALID_DATES'");
            DB::statement('ALTER TABLE project_files MODIFY COLUMN created_at timestamp NULL DEFAULT NULL');
            DB::statement('ALTER TABLE project_files MODIFY COLUMN updated_at timestamp NULL DEFAULT NULL');
        }

        Schema::table('project_files', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });

        Schema::rename('project_files', 'config_files');

        Schema::table('config_files', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::rename('config_files', 'project_files');
    }
}
