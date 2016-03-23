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
            'service'    => Notification::SLACK,
        ]);
    }
}
