<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Variable;

class VariableTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('variables')->delete();

        Variable::create([
            'project_id'     => 1,
            'name'           => 'COMPOSER_PROCESS_TIMEOUT',
            'value'          => '3000'
        ]);
    }
}
