<?php

use Faker\Generator;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\User;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Deployment::class, function (Generator $faker) {
    return [
        'project_id' => function () {
            return factory(Project::class)->create()->id;
        },
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'committer' => $faker->unique()->name,
        'committer' => $faker->unique()->safeEmail,
        'status'    => Deployment::COMPLETED,
        'commit'    => $faker->unique()->sha1,
        'branch'    => 'master',
    ];
});
