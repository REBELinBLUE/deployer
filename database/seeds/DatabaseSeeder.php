<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(ProjectTableSeeder::class);
        $this->call(ServerTableSeeder::class);
        $this->call(DeploymentTableSeeder::class);
        $this->call(CommandTableSeeder::class);
        $this->call(ChannelTableSeeder::class);
        $this->call(HeartbeatTableSeeder::class);
        $this->call(CheckUrlTableSeeder::class);
        $this->call(TemplateTableSeeder::class);
        $this->call(VariableTableSeeder::class);
        $this->call(SharedFileTableSeeder::class);
        $this->call(ConfigFileTableSeeder::class);
    }
}
