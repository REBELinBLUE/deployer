<?php

namespace REBELinBLUE\Deployer\Services\Scripts;

use Illuminate\Log\Writer;
use REBELinBLUE\Deployer\Server;
use Symfony\Component\Process\Process;

/**
 * Class which runs scripts.
 * @mixin Process
 */
class Runner
{
    const TEMPLATE_INPUT = true;
    const DIRECT_INPUT   = false;

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
     * @var Writer
     */
    private $logger;

    /**
     * Runner constructor.
     *
     * @param Parser  $parser
     * @param Process $process
     * @param Writer  $logger
     */
    public function __construct(Parser $parser, Process $process, Writer $logger)
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
    public function __call($method, array $arguments)
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
     * @return $this
     */
    public function setScript($input, array $tokens = [], $script_source = self::TEMPLATE_INPUT)
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
     * @return $this
     */
    public function prependScript($script)
    {
        $this->script = trim($script . PHP_EOL . $this->getScript());

        return $this;
    }

    /**
     * Append commands to the end of the script.
     *
     * @param string $script
     *
     * @return $this
     */
    public function appendScript($script)
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
    public function run($callback = null)
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
     * @param Server $server
     * @param string $private_key
     * @param string $alternative_user
     *
     * @return $this
     */
    public function setServer(Server $server, $private_key, $alternative_user = null)
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
    public function getScript()
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
    private function wrapCommand($script)
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
