<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs\DeployProject;

use REBELinBLUE\Deployer\Jobs\DeployProject\LogFormatter;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\DeployProject\LogFormatter
 */
class LogFormatterTest extends TestCase
{
    /**
     * @dataProvider provideMessages
     * @covers ::error
     * @covers ::format
     */
    public function testError($input, $expected)
    {
        $expected = str_replace('tag', 'error', $expected);

        $formatter = new LogFormatter();
        $actual    = $formatter->error($input);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideMessages
     * @covers ::info
     * @covers ::format
     */
    public function testInfo($input, $expected)
    {
        $expected = str_replace('tag', 'info', $expected);

        $formatter = new LogFormatter();
        $actual    = $formatter->info($input);

        $this->assertSame($expected, $actual);
    }

    public function provideMessages()
    {
        return [
            ['message',                                 '<tag>message</tag>'],
            ['  Leading whitespace',                    '<tag>  Leading whitespace</tag>'],
            ['  Leading with newline' . PHP_EOL,        '<tag>  Leading with newline</tag>' . PHP_EOL],
            ['Trailing whitespace   ',                  '<tag>Trailing whitespace</tag>   '],
            ['Trailing with newline   ' . PHP_EOL,      '<tag>Trailing with newline</tag>   ' . PHP_EOL],
            [' Either end ',                            '<tag> Either end</tag> '],
            [' Either end with newline ' . PHP_EOL,     '<tag> Either end with newline</tag> ' . PHP_EOL],
            ["\t",                                      "\t"],
            ['',                                        ''],
            ['    ',                                    '    '],
            [PHP_EOL,                                   PHP_EOL],
        ];
    }
}
