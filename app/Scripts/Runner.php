<?php

namespace REBELinBLUE\Deployer\Scripts;

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

    public function setTimeout($timeout)
    {
        $this->process->setTimeout($timeout);
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
        $command = $this->wrapCommand();

        $this->process->setCommandLine($command);

        return $this->process->run($callback);
    }

    private function wrapCommand()
    {
        $wrapper = 'RunScriptLocally';
        $tokens = [
            'script' => $this->script,
        ];

        if (!$this->is_local) {
            $wrapper = 'RunScriptOverSSH';
            $tokens = array_merge($tokens, [
                'private_key' => $this->private_key,
                'username'    => $this->server->user,
                'port'        => $this->server->port,
                'ip_address'  => $this->server->ip_address,
            ]);

            // Turn on verbose output so we can see all commands when in debug mode
            if (config('app.debug')) {
                $tokens['script'] = 'set -v' . PHP_EOL . $tokens['script'];
            }
        }

        return with(new Parser)->parseFile($weapper, $tokens);
    }

    /**
     * Runs a script remotely.
     *
     * @param Server        $server
     * @param string        $private_key
     * @param callable|null $callback
     *
     * @return int
     */
    public function setServer(Server $server, $private_key)
    {
        $this->server = $server;
        $this->private_key = $private_key;
        $this->is_local = false;
    }

    public function isSuccessful()
    {
        return $this->process->isSuccessful();
    }

    public function getErrorOutput()
    {
        return $this->process->getErrorOutput();
    }

    public function stop($timeout = 10, $signal = null)
    {
        return $this->process->stop($timeout, $signal);
    }
}
