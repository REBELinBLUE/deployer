<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Scripts;

use REBELinBLUE\Deployer\Services\Scripts\Parser;
use REBELinBLUE\Deployer\Tests\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Services\Scripts\Parser
 */
class ParserTest extends TestCase
{
    /**
     * @covers ::parseString
     */
    public function testParseStringReturnsInputWithNoTokens()
    {
        $expected = 'This is a script';

        $parser = new Parser();
        $actual = $parser->parseString($expected);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::parseString
     */
    public function testParseStringParsersTokens()
    {
        $input    = 'a {{ token }} b';
        $expected = 'a REPLACED b';

        $tokens = ['token' => 'REPLACED'];

        $parser = new Parser();
        $actual = $parser->parseString($input, $tokens);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::parseFile
     */
    public function testParseFileThrowsExceptionWhenFileIsMissing()
    {
        $this->expectException(RuntimeException::class);

        $parser = new Parser();
        $parser->parseFile('a-file-with-does-not-exist');
    }

    public function testParseFileLoadsScript()
    {
        $this->markTestIncomplete('This test has not been implemented yet');
    }
}
