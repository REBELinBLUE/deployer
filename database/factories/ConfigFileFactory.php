<?php

use Faker\Generator;
use REBELinBLUE\Deployer\ConfigFile;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(ConfigFile::class, function (Generator $faker) {
    return [
        'name'    => $faker->word,
        'path'    => $faker->file(base_path(), '/tmp', false),
        'content' => $faker->text,
    ];
});
