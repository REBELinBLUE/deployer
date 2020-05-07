<?php

namespace REBELinBLUE\Deployer\Tests\Unit\stubs;

use Exception;

class ExceptionWithHeaders extends Exception
{
    public function getHeaders(): array
    {
        return [];
    }

    public function getStatusCode(): int
    {
        return 500;
    }
}
