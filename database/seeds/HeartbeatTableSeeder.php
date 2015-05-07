<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Heartbeat;

class HeartbeatTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('heartbeats')->delete();

        Heartbeat::create([
            'name'       => 'My Cron Job',
            'project_id' => 1,
            'interval'   => 30
        ]);
    }
}
