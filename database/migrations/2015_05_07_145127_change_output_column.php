<?php

use Illuminate\Database\Migrations\Migration;

class ChangeOutputColumn extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE server_logs CHANGE output output longtext');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE server_logs CHANGE output output text');
        }
    }
}
