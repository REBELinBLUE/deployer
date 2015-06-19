<?php

use App\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->delete();

        $faker = Faker\Factory::create('en_GB');

        User::create([
            'name'           => 'Stephen Ball',
            'email'          => 'stephen@rebelinblue.com',
            'password'       => bcrypt('password'),
            'remember_token' => str_random(10),
        ]);

        for ($i = 1; $i < 10; $i++) {
            User::create([
                'name'           => $faker->firstName . ' ' . $faker->lastName,
                'email'          => $faker->safeEmail,
                'password'       => bcrypt($faker->password),
                'remember_token' => str_random(10),
            ]);
        }
    }
}
