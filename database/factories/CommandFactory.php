<?php

use Faker\Generator;
use REBELinBLUE\Deployer\Command;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Command::class, function (Generator $faker) {
    return [
        'name'   => $faker->word,
        'user'   => $faker->userName,
        'script' => $faker->text,
    ];
});
