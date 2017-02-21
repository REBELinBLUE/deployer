<?php

namespace REBELinBLUE\Deployer\Services\Token;

use Illuminate\Support\Str;

/**
 * A simple class to generate random string using Laravel's Str::random() static
 * To allow it to be mocked in testing.
 */
class TokenGenerator implements TokenGeneratorInterface
{
    /**
     * Generate a random string.
     *
     * @param  int    $length
     * @return string
     * @see Str::random()
     */
    public function generateRandom($length = 32)
    {
        return Str::random($length);
    }
}
