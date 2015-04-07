<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\User;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->delete();

        $faker = Faker\Factory::create('en_GB');

        User::create([
            'name'     => 'Stephen Ball',
            'email'    => 'stephen@rebelinblue.com',
            'password' => bcrypt('password')
        ]);

        for ($i = 1; $i < 10; $i++) {
            User::create([
                'name'     => $faker->firstName . ' ' . $faker->lastName,
                'email'    => $faker->safeEmail,
                'password' => bcrypt($faker->password)
            ]);
        }
    }
}
