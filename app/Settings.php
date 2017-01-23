<?php

namespace REBELinBLUE\Deployer;

/**
 * Class which contains static methods so that setting values are not duplicated.
 */
class Settings
{
    /**
     * Terminal/Log schemes.
     *
     * @return array
     */
    public function schemes()
    {
        return ['afterglow', 'dawn', 'monokai', 'solarized-dark', 'solarized-light'];
    }

    /**
     * Themes for deployer.
     *
     * @return array
     */
    public function themes()
    {
        return ['blue', 'blue-light',
                'green', 'green-light',
                'purple', 'purple-light',
                'red', 'red-light',
                'yellow', 'yellow-light', ];
    }
}
