<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Deployment;

use Symfony\Component\Process\Process;

class DeploymentTableSeeder extends Seeder {

    public function run()
    {
        DB::table('deployments')->delete();

        $faker = Faker\Factory::create();

        for ($i = 1; $i <= 4; $i++)
        {
            foreach (['Deploying', 'Failed', 'Completed'] as $x => $status)
            {
                Deployment::create([
                    'committer'  => $faker->firstName . ' ' . $faker->lastName,
                    'commit'     => substr($faker->uuid, 0, 8),
                    'project_id' => $i,
                    'user_id'    => rand(1, 10),
                    'status'     => $status,
                    'run'        => $faker->dateTimeThisYear()
                ]);
            }
        }
    }
}
