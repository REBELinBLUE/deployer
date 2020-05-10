<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use REBELinBLUE\Deployer\User;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->delete();

        $faker = Faker\Factory::create('en_GB');

        User::create([
            'name'           => 'Admin',
            'email'          => 'admin@example.com',
            'is_admin'       => 1,
            'password'       => bcrypt('password'),
            'remember_token' => Str::random(10),
        ]);

        for ($i = 1; $i < 10; $i++) {
            User::create([
                'name'           => $faker->firstName . ' ' . $faker->lastName,
                'email'          => $faker->safeEmail,
                'password'       => bcrypt($faker->password),
                'remember_token' => Str::random(10),
            ]);
        }
    }
}
