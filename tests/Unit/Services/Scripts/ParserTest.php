<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Scripts;

use Illuminate\Filesystem\Filesystem;
use Mockery as m;
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

        $parser = app(Parser::class);
        $actual = $parser->parseString($expected);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideFileData
     * @covers ::parseString
     */
    public function testParseStringParsersTokens($input, $expected, array $tokens)
    {
        $parser = app(Parser::class);
        $actual = $parser->parseString($input, $tokens);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::parseFile
     */
    public function testParseFileThrowsExceptionWhenFileIsMissing()
    {
        $this->expectException(RuntimeException::class);

        $parser = app(Parser::class);
        $parser->parseFile('a-file-which-does-not-exist');
    }

    /**
     * @dataProvider provideFileData
     * @covers ::parseFile
     * @covers ::__construct
     */
    public function testParseFileLoadsScript($fileContent, $expected, array $tokens)
    {
        $expectedFileName = 'a-real-file';
        $path             = resource_path('scripts/' . $expectedFileName . '.sh');

        $fs = m::mock(Filesystem::class);
        $fs->shouldReceive('exists')->with($path)->andReturn(true);
        $fs->shouldReceive('get')->with($path)->andReturn($fileContent);

        $parser = new Parser($fs);
        $actual = $parser->parseFile($expectedFileName, $tokens);

        $this->assertSame($actual, $expected);
    }

    public function provideFileData()
    {
        return $this->fixture('Services/Scripts/Parser');
    }
}
