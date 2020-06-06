<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Server;

class ServerTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('servers')->delete();

        Server::create([
            'name'        => 'Web Server',
            'ip_address'  => 'webvm',
            'user'        => 'deploy',
            'path'        => '/var/www',
            'project_id'  => 1,
            'deploy_code' => true,
        ]);

        Server::create([
            'name'        => 'Cron Server',
            'ip_address'  => 'cronvm',
            'user'        => 'deploy',
            'path'        => '/var/www',
            'project_id'  => 1,
            'deploy_code' => true,
        ]);

//        Server::create([
//            'name'        => 'Database VM',
//            'ip_address'  => '192.168.33.70',
//            'user'        => 'deploy',
//            'path'        => '/home/deploy',
//            'project_id'  => 1,
//            'deploy_code' => false,
//        ]);
    }
}
