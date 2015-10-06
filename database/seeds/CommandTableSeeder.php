<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Command;

class CommandTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('commands')->delete();

        Command::create([
            'name'       => 'Welcome',
            'script'     => 'echo "Before Clone {{ release }}"',
            'project_id' => 1,
            'user'       => 'deploy',
            'step'       => Command::BEFORE_CLONE,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'       => 'Goodbye',
            'script'     => 'echo "After Purge {{ release }}"',
            'project_id' => 1,
            'user'       => 'deploy',
            'step'       => Command::AFTER_PURGE,
        ])->servers()->attach([1, 2]);
    }
}
