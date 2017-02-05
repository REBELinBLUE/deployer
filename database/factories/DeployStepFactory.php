<?php

use Faker\Generator;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(DeployStep::class, function (Generator $faker) {
    return [
        'stage' => $faker->randomElement([
            Command::DO_CLONE,
            Command::DO_INSTALL,
            Command::DO_ACTIVATE,
            Command::DO_PURGE,
        ]),
        'deployment_id' => function () {
            return factory(Deployment::class)->create()->id;
        },
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->state(DeployStep::class, 'custom', function (Generator $faker) {
    return [
        'stage' => $faker->randomElement([
            Command::BEFORE_CLONE,
            Command::AFTER_CLONE,
            Command::BEFORE_INSTALL,
            Command::AFTER_INSTALL,
            Command::BEFORE_ACTIVATE,
            Command::AFTER_ACTIVATE,
            Command::BEFORE_PURGE,
            Command::AFTER_PURGE,
        ]),
        'deployment_id' => function () {
            return factory(Deployment::class)->create()->id;
        },
        'command_id' => function () {
            return factory(Command::class)->create()->id;
        },
    ];
});
