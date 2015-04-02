<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Notification;

class NotificationTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('notifications')->delete();

        Notification::create([
            'name'       => 'Deployer',
            'channel'    => '#deploy',
            'icon'       => '',
            'webhook'    => 'https://hooks.slack.com/services/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',
            'project_id' => 1
        ]);
    }
}
