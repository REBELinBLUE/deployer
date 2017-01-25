<?php

namespace REBELinBLUE\Deployer\Tests\Services\Scripts;

use REBELinBLUE\Deployer\Services\Scripts\Parser;
use REBELinBLUE\Deployer\Tests\TestCase;
use RuntimeException;

class ParserTest extends TestCase
{
    public function testParseStringReturnsInputWithNoTokens()
    {
        $expected = 'This is a script';

        $parser = new Parser();
        $actual = $parser->parseString($expected);

        $this->assertEquals($expected, $actual);
    }

    public function testParseStringParsersTokens()
    {
        $input    = 'a {{ token }} b';
        $expected = 'a REPLACED b';

        $tokens = ['token' => 'REPLACED'];

        $parser = new Parser();
        $actual = $parser->parseString($input, $tokens);

        $this->assertEquals($expected, $actual);
    }

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
