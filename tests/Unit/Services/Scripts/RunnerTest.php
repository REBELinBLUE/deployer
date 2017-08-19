<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Scripts;

use Illuminate\Log\Writer;
use Mockery as m;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Services\Scripts\Parser;
use REBELinBLUE\Deployer\Services\Scripts\Runner;
use REBELinBLUE\Deployer\Tests\TestCase;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Services\Scripts\Runner
 */
class RunnerTest extends TestCase
{
    /**
     * @var Runner
     */
    private $process;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Writer
     */
    private $logger;

    protected function setUp()
    {
        parent::setUp();

        $this->parser = m::mock(Parser::class);

        $this->process = m::mock(Process::class);
        $this->process->shouldReceive('stop');

        $this->logger = m::mock(Writer::class);
    }

    public function getRunner()
    {
        return new Runner($this->parser, $this->process, $this->logger);
    }

    public function getRunnerWithScript($script)
    {
        $this->parser->shouldReceive('parseString')->once()->with($script, [])->andReturn($script);

        return $this->getRunner()->setScript($script, [], Runner::DIRECT_INPUT);
    }

    /**
     * @covers ::__construct
     * @covers ::__call
     */
    public function testUnknownMethodsAreDeferredToSymfonyProcess()
    {
        $expected = -10;
        $timeout  = 30;

        $this->process->shouldReceive('getExitCode')->andReturn($expected);
        $this->process->shouldReceive('setTimeout')->with($timeout)->andReturnNull();

        $runner = $this->getRunner();
        $actual = $runner->getExitCode();

        $this->assertSame($expected, $actual);
        $this->assertNull($runner->setTimeout($timeout));
    }

    /**
     * @covers ::__construct
     * @covers ::__call
     */
    public function testUncallableMethodsShouldThrowException()
    {
        $this->expectException(RuntimeException::class);

        $runner = new Runner($this->parser, new Process(''), $this->logger);
        $runner->doSomethingUnreal();
    }

    /**
     * @covers ::setScript
     * @covers ::getScript
     */
    public function testSetScriptParsesDirectInput()
    {
        $input    = 'the script input';
        $tokens   = ['foo' => 'bar'];
        $expected = 'the parsed script';

        $this->parser->shouldReceive('parseString')->once()->with($input, $tokens)->andReturn($expected);

        $runner = $this->getRunner();
        $result = $runner->setScript($input, $tokens, Runner::DIRECT_INPUT);

        $this->assertSame($runner, $result);
        $this->assertSame($expected, $runner->getScript());
    }

    /**
     * @covers ::setScript
     * @covers ::getScript
     */
    public function testSetScriptParsesFileInputWithoutSourceParameter()
    {
        $input    = 'the-file-name';
        $tokens   = ['foo' => 'bar'];
        $expected = 'the parsed script';

        $this->parser->shouldReceive('parseFile')->once()->with($input, $tokens)->andReturn($expected);

        $runner = $this->getRunner();
        $result = $runner->setScript($input, $tokens);

        $this->assertSame($runner, $result);
        $this->assertSame($expected, $runner->getScript());
    }

    /**
     * @covers ::setScript
     * @covers ::getScript
     */
    public function testSetScriptParsesFileInputWithSourceParameter()
    {
        $input    = 'the-file-name';
        $tokens   = ['foo' => 'bar'];
        $expected = 'the parsed script';

        $this->parser->shouldReceive('parseFile')->once()->with($input, $tokens)->andReturn($expected);

        $runner = $this->getRunner();
        $result = $runner->setScript($input, $tokens, Runner::TEMPLATE_INPUT);

        $this->assertSame($runner, $result);
        $this->assertSame($expected, $runner->getScript());
    }

    /**
     * @covers ::prependScript
     * @covers ::getScript
     */
    public function testPrependScript()
    {
        $script   = 'some script';
        $input    = 'prepended';
        $expected = $input . PHP_EOL . $script;

        $runner = $this->getRunnerWithScript($script);
        $result = $runner->prependScript($input);

        $this->assertSame($runner, $result);
        $this->assertSame($expected, $runner->getScript());
    }

    /**
     * @covers ::appendScript
     * @covers ::getScript
     */
    public function testAppendScript()
    {
        $script   = 'some script';
        $input    = 'prepended';
        $expected = $script . PHP_EOL . $input;

        $runner = $this->getRunnerWithScript($script);
        $result = $runner->appendScript($input);

        $this->assertSame($runner, $result);
        $this->assertSame($expected, $runner->getScript());
    }

    /**
     * @covers ::run
     * @covers ::wrapCommand
     */
    public function testRun()
    {
        $tokens   = [];
        $script   = 'this is a script';
        $expected = 'a local script ' . $script;

        $this->parser->shouldReceive('parseString')->with($script, $tokens)->andReturn($script);

        $this->parser->shouldReceive('parseFile')
                     ->with('RunScriptLocally', ['script' => $script])
                     ->andReturn($expected);

        $this->process->shouldReceive('setCommandLine')->with($expected);
        $this->process->shouldReceive('run')->andReturnSelf();
        $this->process->shouldReceive('isSuccessful')->andReturn(true);
        $this->process->shouldNotReceive('getErrorOutput');

        $this->logger->shouldReceive('debug')->with($expected);

        $runner = $this->getRunner();
        $actual = $runner->setScript($script, $tokens, Runner::DIRECT_INPUT)->run();

        $this->assertSame($this->process, $actual);
    }

    /**
     * @covers ::run
     * @covers ::wrapCommand
     */
    public function testRunWithCallback()
    {
        $tokens   = [];
        $script   = 'this is a script';
        $expected = 'a local script ' . $script;
        $callback = function () {
            // A callback function
        };

        $this->parser->shouldReceive('parseString')->with($script, $tokens)->andReturn($script);

        $this->parser->shouldReceive('parseFile')
                     ->with('RunScriptLocally', ['script' => $script])
                     ->andReturn($expected);

        $this->process->shouldReceive('setCommandLine')->with($expected);
        $this->process->shouldReceive('run')->with($callback)->andReturnSelf();
        $this->process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $this->process->shouldNotReceive('getErrorOutput');

        $this->logger->shouldReceive('debug')->with($expected);

        $runner = $this->getRunner();
        $actual = $runner->setScript($script, $tokens, Runner::DIRECT_INPUT)->run($callback);

        $this->assertSame($this->process, $actual);
    }

    /**
     * @covers ::run
     * @covers ::wrapCommand
     */
    public function testRunLogsError()
    {
        $tokens        = [];
        $script        = 'this is a script';
        $wrappedScript = 'a local script ' . $script;
        $expected      = 'error output';

        $this->parser->shouldReceive('parseString')->with($script, $tokens)->andReturn($script);

        $this->parser->shouldReceive('parseFile')
                     ->with('RunScriptLocally', ['script' => $script])
                     ->andReturn($wrappedScript);

        $this->process->shouldReceive('setCommandLine')->with($wrappedScript);
        $this->process->shouldReceive('run')->andReturnSelf();
        $this->process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $this->process->shouldReceive('getErrorOutput')->andReturn($expected);

        $this->logger->shouldReceive('debug')->with($wrappedScript);
        $this->logger->shouldReceive('error')->with($expected);

        $runner = $this->getRunner();
        $actual = $runner->setScript($script, $tokens, Runner::DIRECT_INPUT)->run();

        $this->assertSame($this->process, $actual);
    }

    /**
     * @covers ::run
     * @covers ::wrapCommand
     * @covers ::setServer
     */
    public function testRunOverSSH($alternative_user = null)
    {
        $tokens      = [];
        $script      = 'this is a script';
        $expected    = 'a remote script ' . $script;
        $private_key = 'an-ssh-key';
        $username    = 'server username';
        $port        = 22;
        $ip_address  = 'localhost';

        $server = m::mock(Server::class);
        $server->shouldReceive('getAttribute')->with('private_key')->andReturn($private_key);
        $server->shouldReceive('getAttribute')->with('port')->andReturn($port);
        $server->shouldReceive('getAttribute')->with('ip_address')->andReturn($ip_address);

        if (is_null($alternative_user)) {
            $server->shouldReceive('getAttribute')->with('user')->andReturn($username);
        }

        $expectedTokens = [
            'script'      => $script,
            'private_key' => $private_key,
            'username'    => is_null($alternative_user) ? $username : $alternative_user,
            'port'        => $port,
            'ip_address'  => $ip_address,
        ];

        $this->parser->shouldReceive('parseString')->with($script, $tokens)->andReturn($script);

        $this->parser->shouldReceive('parseFile')
                     ->with('RunScriptOverSSH', $expectedTokens)
                     ->andReturn($expected);

        $this->process->shouldReceive('setCommandLine')->with($expected);
        $this->process->shouldReceive('run')->andReturnSelf();
        $this->process->shouldReceive('isSuccessful')->once()->andReturn(true);

        $this->logger->shouldReceive('debug')->with($expected);

        $runner = $this->getRunner();
        $actual = $runner->setServer($server, $private_key, $alternative_user)
                         ->setScript($script, $tokens, Runner::DIRECT_INPUT)
                         ->run();

        $this->assertSame($this->process, $actual);
    }

    public function testRunOverSSHWithAlternativeUser()
    {
        $this->testRunOverSSH('alternate user');
    }
}
