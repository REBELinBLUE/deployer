<?php

use Illuminate\Database\Migrations\Migration;
use REBELinBLUE\Deployer\Server;

class AddServerStatusFailedFpm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (isset($_ENV['DB_TYPE']) && $_ENV['DB_TYPE'] !== 'sqlite') {
            DB::statement("ALTER TABLE servers CHANGE status status ENUM('"
                . Server::SUCCESSFUL . "', '"
                . Server::TESTING . "', '"
                . Server::FAILED . "', '"
                . Server::UNTESTED . "', '"
                . Server::FAILED_FPM . "') DEFAULT '" . Server::UNTESTED . "'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (isset($_ENV['DB_TYPE']) && $_ENV['DB_TYPE'] !== 'sqlite') {
            DB::statement("ALTER TABLE servers CHANGE status status ENUM('"
                . Server::SUCCESSFUL . "', '"
                . Server::TESTING . "', '"
                . Server::FAILED . "', '"
                . Server::UNTESTED . "') DEFAULT '" . Server::UNTESTED . "'");
        }
    }
}
