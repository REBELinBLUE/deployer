<?php

use App\Command;
use App\CommandTemplate;
use App\Template;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TemplateTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('templates')->delete();
        DB::table('command_templates')->delete();

        Template::create([
            'name' => 'Laravel'
        ]);

        Template::create([
            'name' => 'Wordpress'
        ]);

        CommandTemplate::create([
            'name'        => 'Welcome',
            'script'      => "echo \"Before Clone {{ release }}\"",
            'template_id' => 1,
            'user'        => 'vagrant',
            'step'        => Command::BEFORE_CLONE
        ]);

        CommandTemplate::create([
            'name'        => 'Goodbye',
            'script'      => "echo \"After Purge {{ release }}\"",
            'template_id' => 1,
            'user'        => 'vagrant',
            'step'        => Command::AFTER_PURGE
        ]);
    }
}
