<?php

use Faker\Generator;
use REBELinBLUE\Deployer\Group;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Group::class, function (Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});
