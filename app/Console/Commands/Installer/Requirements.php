<?php

namespace REBELinBLUE\Deployer\Console\Commands\Installer;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Console\Command;
use Illuminate\Queue\QueueManager;
use Illuminate\Redis\RedisManager;
use REBELinBLUE\Deployer\Console\Commands\Traits\GetAvailableOptions;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;

/**
 * Class which checks the installation requirements.
 */
class Requirements
{
    use GetAvailableOptions;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var ConfigRepository
     */
    private $config;

    /**
     * @var RedisManager
     */
    private $redis;

    /**
     * @var QueueManager
     */
    private $queue;

    /**
     * @var bool
     */
    private $errors = false;

    /**
     * @var Command
     */
    private $console;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Requirements constructor.
     *
     * @param Process          $process
     * @param ConfigRepository $config
     * @param RedisManager     $redis
     * @param QueueManager     $queue
     * @param Filesystem       $filesystem
     */
    public function __construct(
        Process $process,
        ConfigRepository $config,
        RedisManager $redis,
        QueueManager $queue,
        Filesystem $filesystem
    ) {
        $this->process    = $process;
        $this->config     = $config;
        $this->redis      = $redis;
        $this->queue      = $queue;
        $this->filesystem = $filesystem;
    }

    /**
     * Checks the system meets all the requirements needed to run Deployer.
     *
     * @param  Command $console
     * @return bool
     */
    public function check(Command $console)
    {
        $this->console = $console;

        $this->versionCheck();
        $this->extensionCheck();
        $this->hasDatabaseDriver();
        $this->disabledFunctionCheck();
        $this->requiredSystemCommands();
        $this->nodeJsCommand();
        $this->checkPermissions();
        $this->checkRedisConnection();
        $this->checkQueueConnection();

        if ($this->errors) {
            $console->line('');
            $console->block('Deployer cannot be installed. Please review the errors above before continuing.');
            $console->line('');
        }

        return !$this->errors;
    }

    /**
     * Checks the PHP version.
     */
    private function versionCheck()
    {
        // Check PHP version:
        if (!version_compare(PHP_VERSION, '7.0.8', '>=')) {
            $this->console->error('PHP 7.0.8 or higher is required');
            $this->errors = true;
        }
    }

    /**
     * Check for required extensions.
     */
    private function extensionCheck()
    {
        // Check for required PHP extensions
        $required_extensions = ['PDO', 'curl', 'gd', 'json', 'tokenizer', 'openssl', 'mbstring'];

        $missing = [];
        foreach ($required_extensions as $extension) {
            if (!extension_loaded($extension)) {
                $missing[] = $extension;
            }
        }

        if (count($missing)) {
            asort($missing);

            $this->console->error('Extension required: ' . implode(', ', $missing));
            $this->errors =  true;
        }
    }

    /**
     * Checks if a DB driver is installed.
     */
    private function hasDatabaseDriver()
    {
        if (!count($this->getDatabaseDrivers())) {
            $this->console->error(
                'At least 1 PDO driver is required. Either sqlite, mysql or pgsql, check your php.ini file'
            );

            $this->errors = true;
        }

        return false;
    }

    /**
     * Checks that required PHP functions are not disabled.
     */
    private function disabledFunctionCheck()
    {
        // Functions needed by symfony process
        if (!function_exists('proc_open')) {
            $this->console->error('Function required: "proc_open". Is it disabled in php.ini?');
            $this->errors = true;
        }
    }

    /**
     * Checks that all the required system commands are available.
     */
    private function requiredSystemCommands()
    {
        // Programs needed in $PATH
        $required_commands = ['ssh', 'ssh-keygen', 'git', 'scp', 'tar', 'gzip', 'rsync', 'bash', 'php'];

        $missing = [];
        foreach ($required_commands as $command) {
            $this->process->setCommandLine('which ' . $command);
            $this->process->setTimeout(null);
            $this->process->run();

            if (!$this->process->isSuccessful()) {
                $missing[] = $command;
            }
        }

        if (count($missing)) {
            asort($missing);

            $this->console->error('Commands not found: ' . implode(', ', $missing));
            $this->errors = true;
        }
    }

    /**
     * Tests that nodejs exists in one of the two possible names.
     */
    private function nodeJsCommand()
    {
        $found = false;
        foreach (['node', 'nodejs'] as $command) {
            $this->process->setCommandLine('which ' . $command);
            $this->process->setTimeout(null);
            $this->process->run();

            if ($this->process->isSuccessful()) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->console->error('node.js was not found');
            $this->errors =  true;
        }
    }

    /**
     * Checks the expected paths are writable.
     */
    private function checkPermissions()
    {
        // Files and directories which need to be writable
        $writable = [
            '.env', 'storage', 'storage/logs', 'storage/app', 'storage/app/mirrors', 'storage/app/tmp',
            'storage/app/public', 'storage/framework', 'storage/framework/cache',
            'storage/framework/sessions', 'storage/framework/views', 'bootstrap/cache',
        ];

        foreach ($writable as $path) {
            if (!$this->filesystem->isWritable(base_path($path))) {
                $this->console->error($path . ' is not writable');
                $this->errors = true;
            }
        }
    }

    /**
     * Checks the connection to redis.
     */
    private function checkRedisConnection()
    {
        // Check that redis is running
        try {
            $this->redis->connection()->ping();
        } catch (\Exception $e) {
            $this->console->error('Redis is not running');
            $this->errors = true;
        }
    }

    /**
     * Checks the connection to the queue.
     */
    private function checkQueueConnection()
    {
        if ($this->config->get('queue.default') === 'beanstalkd') {
            $connected = $this->queue->connection()
                                     ->getPheanstalk()
                                     ->getConnection()
                                     ->isServiceListening();

            if (!$connected) {
                $this->console->error('Beanstalkd is not running');
                $this->errors = true;
            }
        }
    }
}
