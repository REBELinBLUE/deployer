<?php

use Faker\Generator;
use REBELinBLUE\Deployer\Variable;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Variable::class, function (Generator $faker) {
    return [
        'name'  => $faker->word,
        'value' => $faker->word,
    ];
});
