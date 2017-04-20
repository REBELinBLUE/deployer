<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\ServerTemplate;

class ServerTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('server_templates')->delete();

        ServerTemplate::create([
            'name'        => 'Web VM',
            'ip_address'  => '192.168.33.50',
        ]);

        ServerTemplate::create([
            'name'        => 'Cron VM',
            'ip_address'  => '192.168.33.60',
        ]);

        ServerTemplate::create([
            'name'        => 'Database VM',
            'ip_address'  => '192.168.33.70',
        ]);
    }
}
