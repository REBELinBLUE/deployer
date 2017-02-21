<?php

use Faker\Generator;
use REBELinBLUE\Deployer\Template;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Template::class, function (Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});
