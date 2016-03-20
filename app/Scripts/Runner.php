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
        $script = with(new ScriptParser)->parseFile($file, $tokens);

        $cmd = with(new Parser)->parseFile('RunScriptLocally', [
            'script' => $script,
        ]);

        $this->process->setCommandLine($cmd);

        return $this->process->run($callback);
    }

    /**
     * Runs a script remotely.
     */
    public function remote()
    {
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
