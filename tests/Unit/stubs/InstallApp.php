<?php

namespace REBELinBLUE\Deployer\Tests\Unit\stubs;

use REBELinBLUE\Deployer\Console\Commands\InstallApp as RealInstallApp;

/**
 * Wrapper so that secret answers are not secret in tests
 *    This is because, when using coreutils from homebrew on OS X the tests end up outputting various things
 *    such as "stty: 'standard input': Inappropriate ioctl for device", "stdin isn't a terminal" and
 *    "stty: 'standard input': unable to perform all requested operations" when using a hidden input.
 *    This output causes PHPStorm's test runner to crash.
 */
class InstallApp extends RealInstallApp
{
    /**
     * @param string $question
     * @param bool   $fallback
     *
     * @return string
     */
    public function secret($question, $fallback = true)
    {
        return $this->ask($question);
    }

    /**
     * @param string   $question
     * @param array    $choices
     * @param callback $validator
     * @param mixed    $default
     *
     * @return string
     */
    public function askSecretAndValidate($question, array $choices, $validator, $default = null)
    {
        return $this->askAndValidate($question, $choices, $validator, $default, false);
    }
}
