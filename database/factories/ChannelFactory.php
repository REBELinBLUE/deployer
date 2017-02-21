<?php

use Faker\Generator;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Project;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Channel::class, function (Generator $faker) {
    $type = $faker->randomElement([
        Channel::EMAIL,
        Channel::SLACK,
        Channel::HIPCHAT,
        Channel::TWILIO,
        Channel::WEBHOOK,
    ]);

    $config = [];
    if ($type === Channel::EMAIL) {
        $config = ['email' => $faker->safeEmail];
    } elseif ($type === Channel::SLACK) {
        $config = ['webhook' => $faker->url];
    } elseif ($type === Channel::WEBHOOK) {
        $config = ['url' => $faker->url];
    } elseif ($type === Channel::TWILIO) {
        $config = ['telephone' => $faker->e164PhoneNumber];
    } elseif ($type === Channel::HIPCHAT) {
        $config = ['room' => '#' . $faker->word];
    }

    return [
        'name'                       => $faker->word,
        'config'                     => $config,
        'type'                       => $type,
        'on_deployment_success'      => $faker->boolean,
        'on_deployment_failure'      => $faker->boolean,
        'on_link_down'               => $faker->boolean,
        'on_link_still_down'         => $faker->boolean,
        'on_link_recovered'          => $faker->boolean,
        'on_heartbeat_missing'       => $faker->boolean,
        'on_heartbeat_still_missing' => $faker->boolean,
        'on_heartbeat_recovered'     => $faker->boolean,
        'project_id'                 => function () {
            return factory(Project::class)->create()->id;
        },
    ];
});
