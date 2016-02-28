<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\Role;
use REBELinBLUE\Deployer\User;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->delete();

        $faker = Faker\Factory::create('en_GB');

        $admin = User::create([
            'name'           => 'Admin',
            'email'          => 'admin@example.com',
            'password'       => bcrypt('password'),
            'remember_token' => str_random(10),
        ]);

        $admin->assignRole('root');

        $user = User::create([
            'name'           => 'User',
            'email'          => 'user@example.com',
            'password'       => bcrypt('password'),
            'remember_token' => str_random(10),
        ]);

        $user->assignRole('user');

        for ($i = 1; $i < 9; $i++) {
            $user = User::create([
                'name'           => $faker->firstName . ' ' . $faker->lastName,
                'email'          => $faker->safeEmail,
                'password'       => bcrypt($faker->password),
                'remember_token' => str_random(10),
            ]);

            $user->assignRole('user');
        }
    }
}
