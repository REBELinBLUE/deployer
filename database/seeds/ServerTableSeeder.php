<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Server;

class ServerTableSeeder extends Seeder {

    public function run()
    {
        DB::table('servers')->delete();

        // $faker = Faker\Factory::create();

        // $status = ['Successful', 'Testing', 'Failed', 'Untested'];

        // for ($i = 1; $i <= 4; $i++)
        // {
        //     foreach (['Web 1', 'Web 2', 'Cron', 'Database'] as $index => $server)
        //     {
        //         Server::create([
        //             'name'       => $server,
        //             'ip_address' => $faker->localIpv4,
        //             'user'       => 'deploy',
        //             'path'       => '/var/www',
        //             'project_id' => $i,
        //             'status'     => $status[$index]
        //         ]);
        //     }
        // }
        
        Server::create([
            'name'       => 'Vagrant VM',
            'ip_address' => '192.168.33.50',
            'user'       => 'vagrant',
            'path'       => '/var/www',
            'project_id' => 1
        ]);
    }
}
