<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Validators;

use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Validators\HostValidator;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Validators\HostValidator
 */
class HostValidatorTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        if (!filter_var(gethostbyname('www.example.com'), FILTER_VALIDATE_IP)) {
            $this->markTestSkipped('The test can not be run as there was no active network connection');
        }
    }

    /**
     * @dataProvider validationDataProvider
     * @covers ::validate
     */
    public function testValidate($value, $valid)
    {
        $validator = new HostValidator();

        $result = $validator->validate('host', $value, null);

        if ($valid) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function validationDataProvider()
    {
        return $this->fixture('Validators/HostValidator');
    }
}
