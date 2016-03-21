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

    /**
     * Sets the process timeout.
     *
     * @param  int     $timeout
     * @return Process
     */
    public function setTimeout($timeout)
    {
        return $this->process->setTimeout($timeout);
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
     * @return int
     */
    public function setServer(Server $server, $private_key)
    {
        $this->server      = $server;
        $this->private_key = $private_key;
        $this->is_local    = false;
    }

    /**
     * Checks if the process ended successfully.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->process->isSuccessful();
    }

    /**
     * Returns the current error output of the process (STDERR).
     *
     * @return string
     */
    public function getErrorOutput()
    {
        return $this->process->getErrorOutput();
    }

    /**
     * Stops the process.
     *
     * @param  int $timeout
     * @param  int $signal
     * @return int
     */
    public function stop($timeout = 10, $signal = null)
    {
        return $this->process->stop($timeout, $signal);
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
            // Turn on verbose output so we can see all commands when in debug mode
            if (config('app.debug')) {
                $script = 'set -v' . PHP_EOL . $script;
            }

            $wrapper = 'OverSSH';
            $tokens  = [
                'private_key' => $this->private_key,
                'username'    => $this->server->user,
                'port'        => $this->server->port,
                'ip_address'  => $this->server->ip_address,
                'script'      => $script,
            ];
        }

        return with(new Parser)->parseFile('RunScript' . $wrapper, $tokens);
    }
}
