<?php

namespace REBELinBLUE\Deployer\Scripts;

use Symfony\Component\Process\Process;

/**
 * Class which runs scripts.
 */
class Runner
{
    private $process;

    public function __construct()
    {
        $this->process = new Process('');
        $this->process->setTimeout(null);
    }

    public function setTimeout($timeout)
    {
        $this->process->setTimeout($timeout);
    }

    /**
     * Runs a script locally.
     *
     * @param string $file
     * @param array  $tokens
     * @param callable|null $callback
     *
     * @return int
     */
    public function local($file, array $tokens = [], $callback = null)
    {
        $parser = new Parser;

        $script = $parser->parseFile($file, $tokens);

        $cmd = $parser->parseFile('RunScriptLocally', [
            'script' => $script,
        ]);

        $this->process->setCommandLine($cmd);

        return $this->process->run($callback);
    }

    /**
     * Runs a script remotely.
     */
    public function remote(Server $server, $private_key, $file, array $tokens = [], $callback = null)
    {
        $parser = new Parser;

        $script = $parser->parseFile($file, $tokens);

        if (config('app.debug')) {
            // Turn on verbose output so we can see all commands when in debug mode
            $script = 'set -v' . PHP_EOL . $script;
        }

        $cmd = $parser->parseFile('RunScriptOverSSH', [
            'private_key' => $private_key,
            'username'    => $server->user,
            'port'        => $server->port,
            'ip_address'  => $server->ip_address,
            'script'      => $script,
        ]);

        $this->process->setCommandLine($cmd);

        return $this->process->run($callback);
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
