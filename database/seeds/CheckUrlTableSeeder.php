<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\CheckUrl;

class CheckUrlTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('check_urls')->delete();

        CheckUrl::create([
            'title' => 'Deployer',
            'url' => 'http://deploy.app',
            'project_id' => 1,
            'period' => 10,
        ]);
    }
}
