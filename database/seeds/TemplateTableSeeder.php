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
            'name'        => 'Down',
            'script'      => "php artisan down",
            'template_id' => 1,
            'user'        => 'vagrant',
            'step'        => Command::BEFORE_ACTIVATE
        ]);

        CommandTemplate::create([
            'name'        => 'Run Migrations',
            'script'      => "php artisan migrate --force",
            'template_id' => 1,
            'user'        => 'vagrant',
            'step'        => Command::BEFORE_ACTIVATE
        ]);

        CommandTemplate::create([
            'name'        => 'Up',
            'script'      => "php artisan up",
            'template_id' => 1,
            'user'        => 'vagrant',
            'step'        => Command::BEFORE_ACTIVATE
        ]);
    }
}
