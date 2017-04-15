<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Validators;

use phpmock\mockery\PHPMockery as phpm;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Validators\HostValidator;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Validators\HostValidator
 */
class HostValidatorTest extends TestCase
{
    /**
     * @dataProvider validationDataProvider
     * @covers ::validate
     */
    public function testValidate($value, $expected, $address)
    {
        phpm::mock('REBELinBLUE\Deployer\Validators', 'gethostbyname')->andReturn($address);

        $validator = new HostValidator();

        $actual = $validator->validate('host', $value, null);

        $this->assertSame($expected, $actual);
    }

    public function validationDataProvider()
    {
        return $this->fixture('Validators/HostValidator');
    }
}
