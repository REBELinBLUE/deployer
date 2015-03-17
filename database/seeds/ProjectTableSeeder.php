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

        $time = strtotime('-6 months');

        foreach ($status as $index => $state)
        {
            $number = $index + 1;

            $time = strtotime('-' . $index . ' months');

            $key = tempnam(storage_path() . '/app/', 'sshkey');

            $process = new Process(sprintf('ssh-keygen -t rsa -b 2048 -f %s -N "" -C "deploy@deployer"', $key));
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }

            $last_run = rand($time, time());

            Project::create([
                'name' => 'Project ' . $number,
                'repository' => 'git@git.server.com:repostories/project' . $number . '.git',
                'private_key' => 'blah',
                'public_key' => 'blah',
                'url' => 'http://project' . $number . '.app',
                'status' => $state,
                'private_key' => file_get_contents($key),
                'public_key' => file_get_contents($key . '.pub'),
                'last_run' => date('Y-m-d H:i:s', $last_run),
                'build_url' => 'http://ci.rebelinblue.com/build-status/image/' .  $number. '?branch=master'
            ]);

            unlink($key);
            unlink($key . '.pub');
        }
    }
}
