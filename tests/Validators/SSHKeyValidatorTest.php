<?php

use REBELinBLUE\Deployer\Validators\SSHKeyValidator;

class SSHKeyValidatorTest extends TestCase
{
    /**
     * @dataProvider validationDataProvider
     */
    public function testValidate($value, $valid)
    {
        $validator = new SSHKeyValidator;

        $result = $validator->validate('sshkey', $value, null);

        if ($valid) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function validationDataProvider()
    {
        return [
            'Valid Key'          => [$this->getFixtureData('valid_rsa_key'), true],
            'Encrypted key'      => [$this->getFixtureData('encrypted_key'), false],
            'Missing header'     => [$this->getFixtureData('invalid_key_missing_header'), false],
            'Missing footer'     => [$this->getFixtureData('invalid_key_missing_footer'), false],
        ];
    }

    private function getFixtureData($file)
    {
        return file_get_contents(__DIR__ . '/fixtures/' . $file);
    }
}
