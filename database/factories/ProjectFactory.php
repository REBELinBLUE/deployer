<?php

use Faker\Generator;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Project;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Project::class, function (Generator $faker) {
    return [
        'name'       => $faker->word,
        'branch'     => 'master',
        'repository' => 'git@git.example.com:user/repository.git',
        'group_id'   => function () {
            return factory(Group::class)->create()->id;
        },
    ];
});
