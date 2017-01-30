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
     * @param Writer      $log
     */
    protected function configureSingleHandler(Application $app, Writer $log)
    {
        $log->useFiles(
            $this->getFileName($app),
            $app->make('config')->get('app.log_level', 'debug')
        );
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param Application $app
     * @param Writer      $log
     */
    protected function configureDailyHandler(Application $app, Writer $log)
    {
        $config = $app->make('config');

        $log->useDailyFiles(
            $this->getFileName($app),
            $config->get('app.log_max_files', 5),
            $config->get('app.log_level', 'debug')
        );
    }

    /**
     * Determines the filename for the log.
     *
     * @param Application $app
     *
     * @return string
     */
    private function getFileName(Application $app)
    {
        return $app->storagePath() . '/logs/' . php_sapi_name() . '.log';
    }
}
