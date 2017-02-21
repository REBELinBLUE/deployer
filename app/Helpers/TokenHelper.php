<?php

use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;

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
        return app(TokenGeneratorInterface::class)->generateRandom($length);
    }
}
