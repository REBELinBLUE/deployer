<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Validators;

use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Validators\ChannelValidator;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Validators\ChannelValidator
 */
class ChannelValidatorTest extends TestCase
{
    /**
     * @dataProvider validationDataProvider
     * @covers ::validate
     */
    public function testValidate($value, $valid)
    {
        $validator = new ChannelValidator();

        $result = $validator->validate('channel', $value, null);

        if ($valid) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function validationDataProvider()
    {
        return $this->fixture('Validators/ChannelValidator');
    }
}
