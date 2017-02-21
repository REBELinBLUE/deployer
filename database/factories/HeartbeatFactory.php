<?php

use Faker\Generator;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Project;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Heartbeat::class, function (Generator $faker) {
    return [
        'name'       => $faker->word,
        'interval'   => $faker->randomElement([10, 30, 60, 120, 720, 1440, 10080]),
        'project_id' => function () {
            return factory(Project::class)->create()->id;
        },
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->state(Heartbeat::class, 'healthy', function () use ($factory) {
    return array_merge($factory->raw(Heartbeat::class), [
        'status' => Heartbeat::OK,
    ]);
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->state(Heartbeat::class, 'missing', function () use ($factory) {
    return array_merge($factory->raw(Heartbeat::class), [
        'status' => Heartbeat::MISSING,
    ]);
});
