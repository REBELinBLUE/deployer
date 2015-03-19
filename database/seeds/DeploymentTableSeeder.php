<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Deployment;

use Symfony\Component\Process\Process;

class DeploymentTableSeeder extends Seeder {

    public function run()
    {
        DB::table('deployments')->delete();
    }
}
