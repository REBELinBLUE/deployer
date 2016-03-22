<?php

namespace REBELinBLUE\Deployer\Scripts;

use Illuminate\Support\Facades\Log;
use REBELinBLUE\Deployer\Server;
use Symfony\Component\Process\Process;

/**
 * Class which runs scripts.
 */
class Runner
{
    private $process;
    private $script;
    private $server;
    private $private_key;
    private $is_local = true;

    /**
     * Class constructor.
     *
     * @param string $temple
     * @param array  $tokens
     */
    public function __construct($temple, array $tokens = [])
    {
        $this->process = new Process('');
        $this->process->setTimeout(null);

        $this->script = with(new Parser)->parseFile($temple, $tokens);
    }

    /**
     * Runs a script locally.
     *
     * @param  callable|null $callback
     * @return int
     */
    public function run($callback = null)
    {
        $command = $this->wrapCommand($this->script);

        $this->process->setCommandLine($command);

        return $this->process->run($callback);
    }

    /**
     * Sets the script to run on a remote server.
     *
     * @param  Server $server
     * @param  string $private_key
     * @return self
     */
    public function setServer(Server $server, $private_key)
    {
        $this->server      = $server;
        $this->private_key = $private_key;
        $this->is_local    = false;

        return $this;
    }

    /**
     * Wraps the command in either local or remote wrappers.
     *
     * @param  string $script
     * @return string
     */
    private function wrapCommand($script)
    {
        $wrapper = 'Locally';
        $tokens  = [
            'script' => $script,
        ];

        if (!$this->is_local) {
            $wrapper = 'OverSSH';
            $tokens  = array_merge($tokens, [
                'private_key' => $this->private_key,
                'username'    => $this->server->user,
                'port'        => $this->server->port,
                'ip_address'  => $this->server->ip_address,
            ]);
        }

        $output = with(new Parser)->parseFile('RunScript' . $wrapper, $tokens);

        Log::debug($output);

        return $output;
    }

    /**
     * Overloading call to undefined methods to pass them to the process object.
     *
     * @param  string $method
     * @param  array  $arguments
     * @return mixed
     * @throws RuntimeException
     */
    public function __call($method, array $arguments = [])
    {
        if (!is_callable([$this->process, $method])) {
            throw new \RuntimeException('Method ' . $method . ' not exists');
        }

        return call_user_func([$this->process, $method], $arguments);
    }
}
