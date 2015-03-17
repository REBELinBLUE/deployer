<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Project;

use Symfony\Component\Process\Process;

class ProjectTableSeeder extends Seeder {

    public function run()
    {
        DB::table('projects')->delete();

        $status = ['Finished', 'Failed', 'Running', 'Not Deployed'];

        
        $faker = Faker\Factory::create();


        foreach ($status as $index => $state)
        {
            $number = $index + 1;

            $key = tempnam(storage_path() . '/app/', 'sshkey');
            unlink($key);

            $process = new Process(sprintf('ssh-keygen -t rsa -b 2048 -f %s -N "" -C "deploy@deployer"', $key));
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }


            Project::create([
                'name'        => 'Project ' . $number,
                'repository'  => 'git@git.server.com:repostories/project' . $number . '.git',
                'private_key' => 'blah',
                'public_key'  => 'blah',
                'url'         => 'http://project' . $number . '.app',
                'status'      => $state,
                'private_key' => file_get_contents($key),
                'public_key'  => file_get_contents($key . '.pub'),
                'last_run'    => $faker->dateTimeThisYear(),
                'build_url'   => 'http://ci.rebelinblue.com/build-status/image/' .  $number. '?branch=master'
            ]);

            unlink($key);
            unlink($key . '.pub');
        }
    }
}
