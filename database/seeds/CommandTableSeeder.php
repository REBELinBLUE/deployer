<?php

use App\Command;
use Illuminate\Database\Seeder;

class CommandTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('commands')->delete();

        Command::create([
            'name'       => 'Welcome',
            'script'     => 'echo "Before Clone {{ release }}"',
            'project_id' => 1,
            'user'       => 'vagrant',
            'step'       => Command::BEFORE_CLONE,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'       => 'Goodbye',
            'script'     => 'echo "After Purge {{ release }}"',
            'project_id' => 1,
            'user'       => 'vagrant',
            'step'       => Command::AFTER_PURGE,
        ])->servers()->attach([1, 2]);
    }
}
