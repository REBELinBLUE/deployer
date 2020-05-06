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
     *
     * @param string|null $value
     * @param bool        $valid
     * @param string|null $address
     */
    public function testValidate(?string $value, bool $valid, ?string $address)
    {
        phpm::mock('REBELinBLUE\Deployer\Validators', 'gethostbyname')->andReturn($address);

        $validator = new HostValidator();

        $result = $validator->validate('host', $value, null);

        if ($valid) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function validationDataProvider(): array
    {
        return $this->fixture('Validators/HostValidator');
    }
}
