<?php

namespace REBELinBLUE\Deployer\Services\Scripts;

use Psr\Log\LoggerInterface;
use REBELinBLUE\Deployer\Server;
use Symfony\Component\Process\Process;

/**
 * Class which runs scripts.
 * @mixin Process
 */
class Runner
{
    public const TEMPLATE_INPUT = true;
    public const DIRECT_INPUT   = false;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var string
     */
    private $script;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var string
     */
    private $private_key;

    /**
     * @var string
     */
    private $alternative_user;

    /**
     * @var bool
     */
    private $is_local = true;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Runner constructor.
     *
     * @param Parser          $parser
     * @param Process         $process
     * @param LoggerInterface $logger
     */
    public function __construct(Parser $parser, Process $process, LoggerInterface $logger)
    {
        $this->parser  = $parser;
        $this->process = $process;
        $this->logger  = $logger;
    }

    /**
     * Overloading call to undefined methods to pass them to the process object.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @throws \RuntimeException
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        if (!is_callable([$this->process, $method])) {
            throw new \RuntimeException('Method ' . $method . ' not exists');
        }

        return call_user_func_array([$this->process, $method], $arguments);
    }

    /**
     * Sets the script to use for the process.
     *
     * @param string $input
     * @param array  $tokens
     * @param bool   $script_source
     *
     * @return self
     */
    public function setScript(string $input, array $tokens = [], bool $script_source = self::TEMPLATE_INPUT): self
    {
        if ($script_source === self::TEMPLATE_INPUT) {
            $this->script = $this->parser->parseFile($input, $tokens);
        } else {
            $this->script = $this->parser->parseString($input, $tokens);
        }

        return $this;
    }

    /**
     * Prepend commands to the beginning of the script.
     *
     * @param string $script
     *
     * @return self
     */
    public function prependScript(string $script): self
    {
        $this->script = trim($script . PHP_EOL . $this->getScript());

        return $this;
    }

    /**
     * Append commands to the end of the script.
     *
     * @param string $script
     *
     * @return self
     */
    public function appendScript(string $script): self
    {
        $this->script = trim($this->getScript() . PHP_EOL . $script);

        return $this;
    }

    /**
     * Runs a script locally.
     *
     * @param callable|null $callback
     *
     * @return int
     */
    public function run(?callable $callback = null): int
    {
        $command = $this->wrapCommand($this->getScript());

        $this->process->setCommandLine($command);

        $result = $this->process->run($callback);

        if (!$this->process->isSuccessful()) {
            $this->logger->error($this->process->getErrorOutput());
        }

        return $result;
    }

    /**
     * Sets the script to run on a remote server.
     *
     * @param Server      $server
     * @param string      $private_key
     * @param string|null $alternative_user
     *
     * @return self
     */
    public function setServer(Server $server, string $private_key, ?string $alternative_user = null): self
    {
        $this->server           = $server;
        $this->private_key      = $private_key;
        $this->alternative_user = $alternative_user;
        $this->is_local         = false;

        return $this;
    }

    /**
     * Gets the content of the script to be run.
     *
     * @return string
     */
    public function getScript(): string
    {
        return $this->script;
    }

    /**
     * Wraps the command in either local or remote wrappers.
     *
     * @param string $script
     *
     * @return string
     */
    private function wrapCommand(string $script): string
    {
        $wrapper = 'Locally';
        $tokens  = [
            'script' => trim($script),
        ];

        if (!$this->is_local) {
            $wrapper = 'OverSSH';
            $tokens  = array_merge($tokens, [
                'private_key' => $this->private_key,
                'username'    => $this->alternative_user ?: $this->server->user,
                'port'        => $this->server->port,
                'ip_address'  => $this->server->ip_address,
            ]);
        }

        $output = $this->parser->parseFile('RunScript' . $wrapper, $tokens);

        $this->logger->debug($output);

        return $output;
    }
}
