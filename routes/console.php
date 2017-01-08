<?php

Artisan::command('app:version', function () {
    $this->comment(APP_VERSION);
});
