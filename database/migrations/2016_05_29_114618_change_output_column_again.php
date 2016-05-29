<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOutputColumnAgain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (isset($_ENV['DB_TYPE']) && $_ENV['DB_TYPE'] === 'mysql') {
            DB::statement('ALTER TABLE server_logs MODIFY output longtext');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (isset($_ENV['DB_TYPE']) && $_ENV['DB_TYPE'] === 'mysql') {
            DB::statement('ALTER TABLE server_logs MODIFY output text');
        }
    }
}
