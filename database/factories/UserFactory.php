<?php

use Faker\Generator;
use REBELinBLUE\Deployer\User;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Generator $faker) {
    static $password;

    return [
        'name'           => $faker->name,
        'email'          => $faker->unique()->safeEmail,
        'password'       => $password ?: $password = bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});
