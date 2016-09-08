<?php

use Illuminate\Database\Migrations\Migration;

class ChangeOutputColumnAgain extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (isset($_ENV['DB_CONNECTION']) && $_ENV['DB_CONNECTION'] === 'mysql') {
            DB::statement('ALTER TABLE server_logs MODIFY output longtext');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (isset($_ENV['DB_CONNECTION']) && $_ENV['DB_CONNECTION'] === 'mysql') {
            DB::statement('ALTER TABLE server_logs MODIFY output text');
        }
    }
}
