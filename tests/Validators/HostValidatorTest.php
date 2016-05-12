<?php

use REBELinBLUE\Deployer\Validators\HostValidator;

class HostValidatorTest extends TestCase
{
    /**
     * @dataProvider validationDataProvider
     */
    public function testValidate($value, $valid)
    {
        $validator = new HostValidator;

        $result = $validator->validate('host', $value, null);

        if ($valid) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function validationDataProvider()
    {
        return [
            'Empty value'                => ['', false],
            'Null value'                 => [null, false],
            'Out of Range IP'            => ['266.0.0.1', false],
            'Loopback'                   => ['127.0.0.1', true],
            'IPv6 Loopback'              => ['::1', true],
            'Valid IP'                   => ['8.8.8.8', true],
            'Invalid Hostname'           => ['bar', false],
            'Localhost'                  => ['localhost', true],
            'Google'                     => ['google.com', true],
            'Invalid character IPv6'     => ['[::1', false],
            'Invalid hostname character' => [':google.com', false],
            'Web address'                => ['http://www.google.com', false],
            'With path'                  => ['localhost/path', false],
            //'Unicode'                    => ['президент.рф', true],
            'IDNA'                       => ['xn--d1abbgf6aiiy.xn--p1ai', true],
            //'Multibyte'                  => ['스타벅스코리아.com', true],
        ];
    }
}
