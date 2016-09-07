<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Variable;

class VariableTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('variables')->delete();

        Variable::create([
            'name'        => 'COMPOSER_PROCESS_TIMEOUT',
            'value'       => '3000',
            'target_type' => 'project',
            'target_id'   => 1,
        ]);
    }
}
