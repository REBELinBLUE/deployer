<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Validators;

use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Validators\RepositoryValidator;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Validators\RepositoryValidator
 */
class RepositoryValidatorTest extends TestCase
{
    /**
     * @dataProvider validationDataProvider
     * @covers ::validate
     */
    public function testValidate($value, $valid)
    {
        $validator = new RepositoryValidator();

        $result = $validator->validate('repository', $value, null);

        if ($valid) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function validationDataProvider()
    {
        return $this->fixture('Validators/RepositoryValidator');
    }
}
