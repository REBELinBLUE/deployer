<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Template;

class TemplateSeeder extends Seeder
{
    public function run()
    {
        $laravel = Template::create([
            'name' => 'Laravel',
        ]);

        Template::create([
            'name' => 'Wordpress',
        ]);

        Command::create([
            'name'        => 'Down',
            'script'      => 'php artisan down',
            'project_id'  => $laravel->id,
            'user'        => 'deploy',
            'step'        => Command::BEFORE_ACTIVATE,
        ]);

        Command::create([
            'name'        => 'Run Migrations',
            'script'      => 'php artisan migrate --force',
            'project_id'  => $laravel->id,
            'user'        => 'deploy',
            'step'        => Command::BEFORE_ACTIVATE,
        ]);

        Command::create([
            'name'        => 'Up',
            'script'      => 'php artisan up',
            'project_id'  => $laravel->id,
            'user'        => 'deploy',
            'step'        => Command::BEFORE_ACTIVATE,
        ]);
    }
}
