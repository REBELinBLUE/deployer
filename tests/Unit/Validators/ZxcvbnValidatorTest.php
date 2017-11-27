<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Validators;

use Illuminate\Translation\Translator;
use Illuminate\Validation\Validator;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Validators\ZxcvbnValidator;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Validators\ZxcvbnValidatorTest
 */
class ZxcvbnValidatorTest extends TestCase
{
    /**
     * @dataProvider validationDataProvider
     * @covers ::validate
     */
    public function testValidateFailure($value, $expected)
    {
        $translator = m::mock(Translator::class);
        $translator->shouldReceive('get')->with('validation.custom.zxcvbn.' . $expected)->andReturn($expected);

        $mockValidator = m::mock(Validator::class);
        $mockValidator->shouldReceive('setCustomMessages')->with(['zxcvbn' => $expected]);

        $validator = new ZxcvbnValidator($translator);

        $result = $validator->validate('password', $value, 5, $mockValidator);

        $this->assertFalse($result);
    }

    public function validationDataProvider()
    {
        return [
            ['test123456', 'common'],
            ['poiuytghjkl', 'spatial_with_turns'],
            ['poiuyt`', 'straight_spatial'],
            ['98761234', 'sequence'],
            ['30/09/1983', 'dates'],
            ['StephenBall', 'names'],
            ['aaaaaaaaa', 'repeat'],
            ['password', 'top_10'],
            ['trustno1', 'top_100'],
            ['drowssap', 'very_common'],
            ['P4$$w0rd', 'predictable'],
            //['crkuw297', 'Adding a series of digits does not improve security'],
            [date('Y'), 'years']
        ];
    }
}
