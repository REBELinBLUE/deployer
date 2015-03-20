<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Command;

class CommandTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('commands')->delete();

        Command::create([
            'name'       => 'Welcome',
            'script'     => "echo \"Before Clone {{ release }}\"",
            'project_id' => 1,
            'user'       => 'vagrant',
            'step'       => 'Before Clone'
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'       => 'Goodbye',
            'script'     => "echo \"After Purge {{ release }}\"",
            'project_id' => 1,
            'user'       => 'vagrant',
            'step'       => 'After Purge'
        ])->servers()->attach([1, 2]);
    }
}
