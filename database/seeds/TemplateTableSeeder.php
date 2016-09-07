<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Template;

class TemplateTableSeeder extends Seeder
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
            'user'        => 'deploy',
            'step'        => Command::BEFORE_ACTIVATE,
            'target_type' => 'template',
            'target_id'   => $laravel->id,
        ]);

        Command::create([
            'name'        => 'Run Migrations',
            'script'      => 'php artisan migrate --force',
            'user'        => 'deploy',
            'step'        => Command::BEFORE_ACTIVATE,
            'target_type' => 'template',
            'target_id'   => $laravel->id,
        ]);

        Command::create([
            'name'        => 'Up',
            'script'      => 'php artisan up',
            'user'        => 'deploy',
            'step'        => Command::BEFORE_ACTIVATE,
            'target_type' => 'template',
            'target_id'   => $laravel->id,
        ]);
    }
}
