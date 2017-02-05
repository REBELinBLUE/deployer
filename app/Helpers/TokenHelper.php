<?php

use REBELinBLUE\Deployer\Services\Token\TokenGenerator;

/**
 * @codeCoverageIgnoreStart
 */
if (!function_exists('token')) {
    /**
     * Generates a random string for use as tokens.
     *
     * @param int $length
     *
     * @return string
     */
    function token($length)
    {
        return app(TokenGenerator::class)->generateRandom($length);
    }
}
/**
 * @codeCoverageIgnoreEnd
 */
