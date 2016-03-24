<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\SharedFile;

class SharedFileTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('shared_files')->delete();

        SharedFile::create([
            'name' => 'Storage',
            'file' => 'storage/',
            'project_id' => 1,
        ]);

        SharedFile::create([
            'name' => 'Uploads',
            'file' => 'public/uploads/',
            'project_id' => 1,
        ]);
    }
}
