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
            'status' => 'Finished',
            'last_run' => '2015-03-15 12:43:51',
            'build_url' => 'http://ci.rebelinblue.com/build-status/image/1?branch=master'
        ]);

        Project::create([
            'name' => 'Project 2',
            'repository' => 'git@git.server.com:repostories/project2.git',
            'private_key' => 'blah',
            'public_key' => 'blah',
            'url' => 'http://project2.app',
            'status' => 'Failed',
            'last_run' => '2015-03-10 10:03:14',
            'build_url' => 'http://ci.rebelinblue.com/build-status/image/2?branch=master'
        ]);

        Project::create([
            'name' => 'Project 3',
            'repository' => 'git@git.server.com:repostories/project3.git',
            'private_key' => 'blah',
            'public_key' => 'blah',
            'url' => 'http://project3.app',
            'status' => 'Running',
            'last_run' => '2015-03-06 08:31:12',
            'build_url' => 'http://ci.rebelinblue.com/build-status/image/3?branch=master'
        ]);

        Project::create([
            'name' => 'Project 4',
            'repository' => 'git@git.server.com:repostories/project4.git',
            'private_key' => 'blah',
            'public_key' => 'blah',
            'url' => 'http://project4.app',
            'status' => 'Not Deployed',
            'build_url' => 'http://ci.rebelinblue.com/build-status/image/4?branch=master'
        ]);
    }
}
