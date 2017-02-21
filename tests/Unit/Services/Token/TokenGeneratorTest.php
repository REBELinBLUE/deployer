<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Token;

use REBELinBLUE\Deployer\Services\Token\TokenGenerator;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Services\Token\TokenGenerator
 */
class TokenGeneratorTest extends TestCase
{
    /**
     * @covers ::generateRandom
     */
    public function testTokenHasDefaultLength()
    {
        $generator = new TokenGenerator();
        $token     = $generator->generateRandom();

        $this->assertSame(32, strlen($token));
    }

    /**
     * @dataProvider provideExpectedLengths
     * @covers ::generateRandom
     */
    public function testTokenIsCorrectLength($expected)
    {
        $generator = new TokenGenerator();
        $token     = $generator->generateRandom($expected);

        $this->assertSame($expected, strlen($token));
    }

    public function provideExpectedLengths()
    {
        return array_chunk([1, 10, 45, 50, 100], 1);
    }
}
