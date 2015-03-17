<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Server;

class ServerTableSeeder extends Seeder {

    public function run()
    {
        DB::table('servers')->delete();

        $status = ['Successful', 'Testing', 'Failed', 'Not Connected'];
        
        for ($i = 1; $i <= 4; $i++)
        {
            $x = 1;

            foreach (['Web 1', 'Web 2', 'Cron', 'Database'] as $index => $server)
            {

                Server::create([
                    'name' => $server,
                    'ip_address' => '192.168.' . ($i * 100) . '.' . $x,
                    'user' => 'deploy',
                    'path' => '/var/www',
                    'project_id' => $i,
                    'status' => $status[$index]
                ]);

                $x++;
            }
        }
    }
}
