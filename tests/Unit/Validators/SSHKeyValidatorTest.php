<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Validators;

use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Validators\SSHKeyValidator;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Validators\SSHKeyValidator
 */
class SSHKeyValidatorTest extends TestCase
{
    /**
     * @dataProvider provideKeys
     * @covers ::validate
     */
    public function testValidate($value, $valid)
    {
        $validator = new SSHKeyValidator();

        $result = $validator->validate('sshkey', $value, null);

        if ($valid) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function provideKeys()
    {
        return $this->fixture('Validators/SSHKeyValidator');
    }
}
