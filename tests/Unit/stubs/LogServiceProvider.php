<?php

namespace REBELinBLUE\Deployer\Tests\Unit\stubs;

use Illuminate\Log\Logger;
use REBELinBLUE\Deployer\Providers\LogServiceProvider as ServiceProvider;

/**
 * A stub class to make the protected methods public for testing.
 *
 * Could instead call the createLogger() method but that means we'd end up
 * having to mock a lot more of the \Illuminate\Log\LogServiceProvider
 * internals structure/calls.
 */
class LogServiceProvider extends ServiceProvider
{
    public function configureSingleHandler(Logger $log)
    {
        parent::configureSingleHandler($log);
    }

    public function configureDailyHandler(Logger $log)
    {
        parent::configureDailyHandler($log);
    }
}
