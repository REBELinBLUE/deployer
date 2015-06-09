<?php

use Illuminate\Database\Migrations\Migration;

class ChangeOutputColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ($_ENV['DB_TYPE'] != 'sqlite') {
            DB::statement('ALTER TABLE server_logs CHANGE output output longtext');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ($_ENV['DB_TYPE'] != 'sqlite') {
            DB::statement('ALTER TABLE server_logs CHANGE output output text');
        }
    }
}
