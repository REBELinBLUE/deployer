<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call('UserTableSeeder');
        $this->call('ProjectTableSeeder');
        $this->call('ServerTableSeeder');
        $this->call('DeploymentTableSeeder');
        $this->call('CommandTableSeeder');
        $this->call('NotificationTableSeeder');
        $this->call('HeartbeatTableSeeder');
        $this->call('TemplateTableSeeder');
    }
}
