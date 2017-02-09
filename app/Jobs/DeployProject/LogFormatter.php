<?php

namespace REBELinBLUE\Deployer\Jobs\DeployProject;

/**
 * Format the console log lines.
 */
class LogFormatter
{
    /**
     * Format an error message.
     *
     * @param $message
     *
     * @return string
     */
    public function error($message)
    {
        return '<error>' . $message . '</error>';
    }

    /**
     * Format an info message.
     *
     * @param $message
     *
     * @return string
     */
    public function info($message)
    {
        return '<info>' . $message . '</info>';
    }
}
