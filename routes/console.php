<?php

use REBELinBLUE\Deployer\Contracts\Github\LatestReleaseInterface;
use Illuminate\Support\Facades\Artisan;
use Version\Version;

Artisan::command('app:version', function (LatestReleaseInterface $release) {
    $latest_release = $release->latest();

    $current = Version::parse(APP_VERSION);
    $latest  = Version::parse($latest_release);

    if ($latest->compare($current) === 1) {
        $this->info('There is an update available!' . PHP_EOL);
    }

    $this->table(['Installed Release', 'Current Release'], [
        [APP_VERSION, $latest_release],
    ]);
})->describe('Show the installed app version');
