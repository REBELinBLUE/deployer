<?php

namespace REBELinBLUE\Deployer\Tests\Unit\stubs;

use Exception;

class ExceptionWithHeaders extends Exception
{
    public function getHeaders()
    {
        return [];
    }

    public function getStatusCode()
    {
        return 500;
    }
}
