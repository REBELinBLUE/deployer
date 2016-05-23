<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Notification;

class NotificationTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('notifications')->delete();

        Notification::create([
            'name'       => 'Deployer',
            'channel'    => '#testing',
            'icon'       => ':ghost:',
            'webhook'    => 'https://hooks.slack.com/services/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',
            'project_id' => 1,
        ]);
    }
}
