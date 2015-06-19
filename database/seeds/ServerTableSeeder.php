<?php

use App\Server;
use Illuminate\Database\Seeder;

class ServerTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('servers')->delete();

        Server::create([
            'name'       => 'Web VM',
            'ip_address' => '192.168.33.50',
            'user'       => 'vagrant',
            'path'       => '/var/www',
            'project_id' => 1,
        ]);

        Server::create([
            'name'       => 'Cron VM',
            'ip_address' => '192.168.33.60',
            'user'       => 'vagrant',
            'path'       => '/var/www',
            'project_id' => 1,
        ]);

        // Server::create([
        //     'name'       => 'DB VM',
        //     'ip_address' => '192.168.33.70',
        //     'user'       => 'vagrant',
        //     'path'       => '/var/www',
        //     'project_id' => 1
        // ]);
    }
}
