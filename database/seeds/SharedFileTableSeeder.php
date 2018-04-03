<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\SharedFile;

class SharedFileTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('shared_files')->delete();

        SharedFile::create([
            'name'        => 'Storage',
            'file'        => 'storage/',
            'target_type' => 'project',
            'target_id'   => 1,
        ]);

        SharedFile::create([
            'name'        => 'README',
            'file'        => 'README.md',
            'target_type' => 'project',
            'target_id'   => 1,
        ]);

        SharedFile::create([
            'name'        => 'LICENSE',
            'file'        => '/LICENSE.md',
            'target_type' => 'project',
            'target_id'   => 1,
        ]);

//        SharedFile::create([
//            'name'        => 'CSS',
//            'file'        => 'resources/assets/css/console.css',
//            'target_type' => 'project',
//            'target_id'   => 1,
//        ]);
    }
}
