<?php

use Faker\Generator;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Server;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Server::class, function (Generator $faker) {
    return [
        'name'        => $faker->word,
        'user'        => $faker->userName,
        'port'        => 22,
        'path'        => '/var/www',
        'deploy_code' => $faker->boolean,
        'ip_address'  => $faker->unique()->ipv4,
        'project_id'  => function () {
            return factory(Project::class)->create()->id;
        },
    ];
});
