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
        return [
            'Empty value'        => ['', false],
            'Null value'         => [null, false],
            'Protocol only'      => ['http:', false],
            'Hostname only'      => ['github.com', false],
            'IP address only'    => ['8.8.8.8', false],
            'HTTP host'          => ['http://github.com', true],
            'HTTPS host'         => ['https://github.com', true],
            'SSH host'           => ['ssh://github.com', true],
            'Git host'           => ['git://github.com', true],
            'Missing username'   => ['gitlab.com:namespace/repo.git', false],
            'Missing repo'       => ['git@gitlab.com', false],
            'Missing namespamce' => ['git@gitlab.com:repo.git', false],
            'Missing extension'  => ['git@gitlab.com:namespace/repo', false],
            'User repository'    => ['git@gitlab.com:namespace/repo.git', true],
        ];
    }
}
