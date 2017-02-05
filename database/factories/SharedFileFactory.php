<?php

use Faker\Generator;
use REBELinBLUE\Deployer\SharedFile;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(SharedFile::class, function (Generator $faker) {
    return [
        'name' => $faker->word,
        'file' => $faker->file(base_path(), '/tmp', false),
    ];
});
