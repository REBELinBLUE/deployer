<?php

use Faker\Generator;
use REBELinBLUE\Deployer\Ref;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Ref::class, function (Generator $faker) {
    return [
        'name'       => $faker->word,
        'is_tag'     => $faker->boolean,
        'project_id' => function () {
            return factory(Project::class)->create()->id;
        },
    ];
});
