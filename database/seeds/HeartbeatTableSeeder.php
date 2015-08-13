<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Heartbeat;

class HeartbeatTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('heartbeats')->delete();

        Heartbeat::create([
            'name'       => 'My Cron Job',
            'project_id' => 1,
            'interval'   => 30,
        ]);
    }
}
