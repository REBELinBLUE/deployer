<?php

use REBELinBLUE\Deployer\Validators\ChannelValidator;

class ChannelValidatorTest extends TestCase
{
    /**
     * @dataProvider validationDataProvider
     */
    public function testValidate($value, $valid)
    {
        $validator = new ChannelValidator;

        $result = $validator->validate('channel', $value, null);

        if ($valid) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function validationDataProvider()
    {
        return [
            'Empty value'    => ['', false],
            'Null value'     => [null, false],
            'No prefix'      => ['channel', false],
            'Invalid prefix' => ['$channel', false],
            'Only hash'      => ['#', false],
            'Only at'        => ['@', false],
            'Valid channel'  => ['#channel', true],
            'Valid person'   => ['@username', true],
        ];
    }
}
