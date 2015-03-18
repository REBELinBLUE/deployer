<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Command;

class CommandTableSeeder extends Seeder {

    public function run()
    {
        DB::table('commands')->delete();

        for ($i = 1; $i <= 3; $i++)
        {
            foreach (['Clone', 'Install', 'Activate', 'Purge'] as $action)
            {
                foreach (['Before', 'After'] as $order)
                {
                    $step = $order . ' ' . $action;
                    
                    Command::create([
                        'name'       => $step,
                        'script'     => "cd {{release}}\necho \"{$step}\"",
                        'project_id' => $i,
                        'step'       => $step
                    ]);
                }
            }
        }
    }

}