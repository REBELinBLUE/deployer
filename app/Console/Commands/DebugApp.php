<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles;
use REBELinBLUE\Deployer\Events\RestartSocketServer;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;

/**
 * A console command to easily toggle debugging.
 */
class DebugApp extends Command
{
    use OutputStyles;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug {status=on : Whether to turn debugging on or off}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows debugging to easily be switched on or off';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @param Dispatcher $dispatcher
     */
    public function handle(Dispatcher $dispatcher)
    {
        $env = base_path('.env');

        $content = $this->filesystem->get($env);

        $enable = ($this->argument('status') === 'on');

        $this->line('');

        if ($enable) {
            $this->block('Enabling debug mode', 'error');
        } else {
            $this->block('Disabling debug mode', 'fg=black;bg=green');
        }

        $this->line('');

        $content = $this->replaceConfigValues($content, $enable);

        $this->info('Configuration file updated');
        $this->filesystem->put($env, $content);

        $this->call('config:clear');
        $this->call('queue:restart');

        $this->info('Restarting the socket server');
        $dispatcher->dispatch(new RestartSocketServer());
    }

    /**
     * @param string $content The content of the config file
     * @param bool   $enable  Whether to enable debugging
     *
     * @return string
     */
    private function replaceConfigValues($content, $enable = true)
    {
        $debug = $enable ? 'true' : 'false';
        $level = $enable ? 'debug' : 'error';

        return preg_replace([
            '/APP_DEBUG=(.*)[\n]/',
            '/APP_LOG_LEVEL=(.*)[\n]/',
        ], [
            'APP_DEBUG=' . $debug . PHP_EOL,
            'APP_LOG_LEVEL=' . $level . PHP_EOL,
        ], $content);
    }
}
