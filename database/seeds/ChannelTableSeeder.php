<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Channel;

class ChannelTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('channels')->delete();

        Channel::create([
            'project_id' => 1,
            'type'       => 'mail',
            'name'       => 'Admin',
            'config'     => [
                'email' => 'admin@example.com',
            ],
            'on_deployment_success'      => true,
            'on_deployment_failure'      => true,
            'on_link_down'               => true,
            'on_link_still_down'         => true,
            'on_link_recovered'          => true,
            'on_heartbeat_missing'       => true,
            'on_heartbeat_still_missing' => true,
            'on_heartbeat_recovered'     => true,
        ]);
    }
}
