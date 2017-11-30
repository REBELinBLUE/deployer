<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Environment;

class EnvironmentTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('environments')->delete();

        Environment::create([
            'project_id'  => 1,
            'name'        => 'Staging',
            'description' => 'Staging',
        ]);

        Environment::create([
            'project_id'  => 1,
            'name'        => 'QA',
            'default_on'  => false,
            'description' => 'QA',
        ]);


        Environment::create([
            'project_id'   => 1,
            'name'         => 'Production',
            'default_on'   => false,
            'description'  => 'Production',
        ]);
    }
}
