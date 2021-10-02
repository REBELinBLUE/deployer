<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Server;

class ServerTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('servers')->delete();

        Server::create([
            'name'        => 'Web VM',
            'ip_address'  => 'web.dev',
            'user'        => 'deploy',
            'path'        => '/var/www',
            'project_id'  => 1,
            'deploy_code' => true,
        ]);

        Server::create([
            'name'        => 'Cron VM',
            'ip_address'  => 'cron.dev',
            'user'        => 'deploy',
            'path'        => '/var/www',
            'project_id'  => 1,
            'deploy_code' => true,
        ]);

        Server::create([
            'name'        => 'Database VM',
            'ip_address'  => 'db.dev',
            'user'        => 'deploy',
            'path'        => '/home/deploy',
            'project_id'  => 1,
            'deploy_code' => false,
        ]);
    }
}
