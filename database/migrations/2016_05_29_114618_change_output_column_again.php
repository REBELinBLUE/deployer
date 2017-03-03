<?php

use Illuminate\Database\Migrations\Migration;

class ChangeOutputColumnAgain extends Migration
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
        }
    }
}
