<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Command;

class CommandTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('commands')->delete();

        Command::create([
            'name'        => 'Before Create New Release',
            'script'      => $this->getScript(),
            'user'        => '',
            'step'        => Command::BEFORE_CLONE,
            'optional'    => true,
            'target_type' => 'project',
            'target_id'   => 1,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'        => 'After Create New Release',
            'script'      => $this->getScript(),
            'user'        => '',
            'step'        => Command::AFTER_CLONE,
            'target_type' => 'project',
            'target_id'   => 1,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'        => 'Before Install',
            'script'      => $this->getScript(),
            'user'        => 'deploy',
            'step'        => Command::BEFORE_INSTALL,
            'target_type' => 'project',
            'target_id'   => 1,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'        => 'After Install',
            'script'      => $this->getScript(),
            'user'        => 'deploy',
            'step'        => Command::AFTER_INSTALL,
            'target_type' => 'project',
            'target_id'   => 1,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'        => 'Before Activate',
            'script'      => $this->getScript(),
            'user'        => 'deploy',
            'step'        => Command::BEFORE_ACTIVATE,
            'target_type' => 'project',
            'target_id'   => 1,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'        => 'After Activate',
            'script'      => $this->getScript(),
            'user'        => 'deploy',
            'step'        => Command::AFTER_ACTIVATE,
            'target_type' => 'project',
            'target_id'   => 1,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'        => 'Before Purge',
            'script'      => $this->getScript(),
            'user'        => 'deploy',
            'step'        => Command::BEFORE_PURGE,
            'target_type' => 'project',
            'target_id'   => 1,
        ])->servers()->attach([1, 2]);

        Command::create([
            'name'        => 'After Purge',
            'script'      => $this->getScript(),
            'user'        => 'deploy',
            'step'        => Command::AFTER_PURGE,
            'optional'    => true,
            'target_type' => 'project',
            'target_id'   => 1,
        ])->servers()->attach([1, 2]);
    }

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
echo "Server user \$(whoami)"
EOD;
    }
}
