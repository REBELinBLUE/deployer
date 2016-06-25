<?php

namespace REBELinBLUE\Deployer\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\ConfigureLogging as BaseLoggingConfiguration;
use Illuminate\Log\Writer;

/**
 * Configure the logging, split the CLI log file with the web log file.
 */
class ConfigureLogging extends BaseLoggingConfiguration
{
    /**
     * Configure the Monolog handlers for the application.
     *
     * @param Application $app
     * @param Writer $log
     */
    protected function configureSingleHandler(Application $app, Writer $log)
    {
        $fileName = php_sapi_name();
        $log->useFiles($app->storagePath() . '/logs/' . $fileName . '.log');
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param Application $app
     * @param Writer $log
     */
    protected function configureDailyHandler(Application $app, Writer $log)
    {
        $fileName = php_sapi_name();
        $log->useDailyFiles(
            $app->storagePath() . '/logs/' . $fileName . '.log',
            $app->make('config')->get('app.log_max_files', 5)
        );
    }
}
