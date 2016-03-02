<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Command;

class CommandTableSeeder extends Seeder
{
    private function getScript()
    {
        return <<< EOD
echo "Release {{ release }}"
echo "Release Path {{ release_path }}"
echo "Project Path {{ project_path }}"
echo "Branch {{ branch }}"
echo "SHA {{ sha }}"
echo "Short SHA {{ short_sha }}"
echo "Deployer email {{ deployer_email }}"
echo "Deployer name {{ deployer_name }}"
echo "Committer email {{ committer_email }}"
echo "Committer name {{ committer_name }}"
EOD;
    }

    public function run()
    {
        DB::table('commands')->delete();

        Command::create([
            'name'       => 'Before Clone',
            'script'     => $this->getScript(),
            'project_id' => 1,
            'user'       => 'deploy',
            'step'       => Command::BEFORE_CLONE,
            'optional'   => true,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'       => 'After Clone',
            'script'     => $this->getScript(),
            'project_id' => 1,
            'user'       => 'deploy',
            'step'       => Command::AFTER_CLONE,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'       => 'Before Install',
            'script'     => $this->getScript(),
            'project_id' => 1,
            'user'       => 'deploy',
            'step'       => Command::BEFORE_INSTALL,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'       => 'After Install',
            'script'     => $this->getScript(),
            'project_id' => 1,
            'user'       => 'deploy',
            'step'       => Command::AFTER_INSTALL,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'       => 'Before Activate',
            'script'     => $this->getScript(),
            'project_id' => 1,
            'user'       => 'deploy',
            'step'       => Command::BEFORE_ACTIVATE,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'       => 'After Activate',
            'script'     => $this->getScript(),
            'project_id' => 1,
            'user'       => 'deploy',
            'step'       => Command::AFTER_ACTIVATE,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'       => 'Before Purge',
            'script'     => $this->getScript(),
            'project_id' => 1,
            'user'       => 'deploy',
            'step'       => Command::BEFORE_PURGE,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'       => 'After Purge',
            'script'     => $this->getScript(),
            'project_id' => 1,
            'user'       => 'deploy',
            'step'       => Command::AFTER_PURGE,
            'optional'   => true,
        ])->servers()->attach([1, 2]);
    }
}
