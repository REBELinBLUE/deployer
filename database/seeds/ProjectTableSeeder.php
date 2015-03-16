<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Project;

class ProjectTableSeeder extends Seeder {

    public function run()
    {
        DB::table('projects')->delete();

        Project::create([
            'name' => 'Project 1',
            'repository' => 'git@git.server.com:repostories/project1.git',
            'private_key' => 'blah',
            'public_key' => 'blah',
            'url' => 'http://project1.app',
            'build_url' => 'http://ci.local/project1.png'
        ]);

        Project::create([
            'name' => 'Project 2',
            'repository' => 'git@git.server.com:repostories/project2.git',
            'private_key' => 'blah',
            'public_key' => 'blah',
            'url' => 'http://project2.app',
            'build_url' => 'http://ci.local/project2.png'
        ]);

        Project::create([
            'name' => 'Project 3',
            'repository' => 'git@git.server.com:repostories/project3.git',
            'private_key' => 'blah',
            'public_key' => 'blah',
            'url' => 'http://project3.app',
            'build_url' => 'http://ci.local/project3.png'
        ]);

        Project::create([
            'name' => 'Project 4',
            'repository' => 'git@git.server.com:repostories/project4.git',
            'private_key' => 'blah',
            'public_key' => 'blah',
            'url' => 'http://project4.app',
            'build_url' => 'http://ci.local/project4.png'
        ]);
    }
}