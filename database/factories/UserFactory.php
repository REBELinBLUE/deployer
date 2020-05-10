<?php

use Faker\Generator;
use Illuminate\Support\Str;
use REBELinBLUE\Deployer\User;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Generator $faker) {
    static $password;

    return [
        'name'           => $faker->name,
        'email'          => $faker->unique()->safeEmail,
        'password'       => $password ?: $password = bcrypt(Str::random(10)),
        'remember_token' => Str::random(10),
        'is_admin'       => 1,
    ];
});
