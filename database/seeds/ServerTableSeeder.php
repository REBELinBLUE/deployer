<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Server;

class ServerTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('servers')->delete();

        Server::create([
            'name'       => 'Web VM',
            'ip_address' => '192.168.33.50',
            'user'       => 'deploy',
            'path'       => '/var/www',
            'project_id' => 1,
        ]);

        Server::create([
            'name'       => 'Cron VM',
            'ip_address' => '192.168.33.60',
            'user'       => 'deploy',
            'path'       => '/var/www',
            'project_id' => 1,
        ]);

        // Server::create([
        //     'name'       => 'DB VM',
        //     'ip_address' => '192.168.33.70',
        //     'user'       => 'deploy',
        //     'path'       => '/var/www',
        //     'project_id' => 1
        // ]);
    }
}
