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

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->defineAs(Ref::class, 'branch', function () use ($factory) {
    return array_merge($factory->raw(Ref::class), [
        'is_tag' => false,
    ]);
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->defineAs(Ref::class, 'tag', function () use ($factory) {
    return array_merge($factory->raw(Ref::class), [
        'is_tag' => true,
    ]);
});
