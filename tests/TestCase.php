<?php

namespace REBELinBLUE\Deployer\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery as m;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @todo Check jobs and events receive expected parameters
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function assertSameTimestamp($timestamp, $field)
    {
        $this->assertSame($timestamp, $field->format('Y-m-d H:i:s'));
    }

    protected function mockTokenGenerator($token)
    {
        $generator = m::mock(TokenGeneratorInterface::class);
        $generator->shouldReceive('generateRandom')->with(m::type('int'))->andReturn($token);

        $this->app->instance(TokenGeneratorInterface::class, $generator);

        return $generator;
    }

    /**
     * Loads a YML file into an array and returns the root 'fixture' element.
     *
     * @param string $file
     *
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     *
     * @return array
     */
    protected function fixture($file)
    {
        $path = dirname(__FILE__) . '/Unit/data/' . $file . '.yml';
        $data = Yaml::parse(file_get_contents($path), Yaml::PARSE_CONSTANT | YAML::PARSE_EXCEPTION_ON_INVALID_TYPE);

        return $data['fixture'];
    }
}
