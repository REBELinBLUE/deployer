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

        $root = Role::findOrFail(1);

        $admin->assignRole($root);

        for ($i = 1; $i < 10; $i++) {
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
