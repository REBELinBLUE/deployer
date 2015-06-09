<?php

use Illuminate\Database\Seeder;

class DeploymentTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('deployments')->delete();
    }
}
