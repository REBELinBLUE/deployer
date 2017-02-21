<?php

namespace REBELinBLUE\Deployer\Services\Token;

interface TokenGeneratorInterface
{
    /**
     * Generate a random string.
     *
     * @param  int    $length
     * @return string
     */
    public function generateRandom($length = 32);
}
