<?php

use App\Command;
use App\Project;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run()
    {
        $laravel = Project::create([
            'name'        => 'Laravel',
            'is_template' => true,
            'group_id'    => 1,
        ]);

        Project::create([
            'name'        => 'Wordpress',
            'is_template' => true,
            'group_id'    => 1,
        ]);

        Command::create([
            'name'        => 'Down',
            'script'      => 'php artisan down',
            'project_id'  => $laravel->id,
            'user'        => 'vagrant',
            'step'        => Command::BEFORE_ACTIVATE,
        ]);

        Command::create([
            'name'        => 'Run Migrations',
            'script'      => 'php artisan migrate --force',
            'project_id'  => $laravel->id,
            'user'        => 'vagrant',
            'step'        => Command::BEFORE_ACTIVATE,
        ]);

        Command::create([
            'name'        => 'Up',
            'script'      => 'php artisan up',
            'project_id'  => $laravel->id,
            'user'        => 'vagrant',
            'step'        => Command::BEFORE_ACTIVATE,
        ]);
    }
}
