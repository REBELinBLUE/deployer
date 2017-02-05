<?php

use Faker\Generator;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Project;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(CheckUrl::class, function (Generator $faker) {
    return [
        'name'       => $faker->word,
        'url'        => $faker->url,
        'period'     => $faker->randomElement([60, 30, 10, 5]),
        'missed'     => 0,
        'project_id' => function () {
            return factory(Project::class)->create()->id;
        },
    ];
});
