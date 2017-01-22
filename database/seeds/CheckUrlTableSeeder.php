<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\CheckUrl;

class CheckUrlTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('check_urls')->delete();

        CheckUrl::create([
            'name'       => 'Deployer',
            'url'        => 'http://deployer.app',
            'project_id' => 1,
            'period'     => 10,
        ]);
    }
}
