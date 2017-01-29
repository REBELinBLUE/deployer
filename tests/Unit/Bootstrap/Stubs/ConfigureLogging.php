<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Bootstrap\Stubs;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\Writer;
use REBELinBLUE\Deployer\Bootstrap\ConfigureLogging as AppConfigureLogging;

class ConfigureLogging extends AppConfigureLogging
{
    public function configureSingleHandler(Application $app, Writer $log)
    {
        parent::configureSingleHandler($app, $log);
    }

    public function configureDailyHandler(Application $app, Writer $log)
    {
        parent::configureDailyHandler($app, $log);
    }
}
