<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(ProjectTableSeeder::class);
        $this->call(ServerTableSeeder::class);
        $this->call(DeploymentTableSeeder::class);
        $this->call(CommandTableSeeder::class);
        $this->call(NotificationTableSeeder::class);
        $this->call(HeartbeatTableSeeder::class);
        $this->call(TemplateSeeder::class);
        $this->call(VariableTableSeeder::class);
    }
}
