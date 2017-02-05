<?php

use Faker\Generator;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\ServerLog;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(ServerLog::class, function (Generator $faker) {
    return [
        'status'    => ServerLog::COMPLETED,
        'output'    => $faker->text,
        'server_id' => function () {
            return factory(Server::class)->create()->id;
        },
        'deploy_step_id' => function () {
            return factory(DeployStep::class)->create()->id;
        },
    ];
});
