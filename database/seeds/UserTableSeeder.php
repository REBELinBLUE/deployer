<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\User;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->delete();

        $faker = Faker\Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name'     => $faker->firstName . ' ' . $faker->lastName,
                'email'    => $faker->safeEmail,
                'password' => md5($faker->safeEmail)
            ]);
        }
    }
}
