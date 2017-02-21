<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;
use REBELinBLUE\Deployer\Services\Update\LatestReleaseInterface;
use Symfony\Component\Console\Helper\FormatterHelper;

/**
 * Shows the app version and checks for updates.
 */
class AppVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the installed app version';

    /**
     * @var LatestReleaseInterface
     */
    private $release;

    /**
     * Create a new command instance.
     *
     * @param LatestReleaseInterface $release
     */
    public function __construct(LatestReleaseInterface $release)
    {
        parent::__construct();

        $this->release = $release;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $latest_release = $this->release->latest();

        $this->line('');
        if (!$this->release->isUpToDate()) {
            $this->line($this->updateBanner());
            $this->line('');
            $this->table(['Installed Release', 'Current Release'], [[APP_VERSION, $latest_release]]);
        } else {
            $this->line('You are already running the latest version <comment>' . APP_VERSION . '</comment>');
        }
    }

    /**
     * Generates the update banner.
     *
     * @return string
     */
    private function updateBanner()
    {
        $formatter = new FormatterHelper();

        return $formatter->formatBlock('There is an update available!', 'fg=white;bg=green;options=bold', true);
    }
}
