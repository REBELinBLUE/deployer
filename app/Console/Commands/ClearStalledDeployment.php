<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\ServerLog;

/**
 * Clears any stalled deployments so that new deployments can be queued.
 */
class ClearStalledDeployment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployer:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancels any stalled deployments so new deployments can be run';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $bring_back_up = false;

        // Check the app is offline, if not ask the user if it can be brought down
        if (!App::isDownForMaintenance()) {
            $this->error(Lang::get('app.not_down'));

            if (!$this->confirm(Lang::get('app.switch_down'))) {
                return;
            }

            $bring_back_up = true;

            $this->call('down');
        }

        $this->cleanupDeployments();

        // If we prompted the user to bring the app down, bring it back up
        if ($bring_back_up) {
            $this->call('up');
        }
    }

    /**
     * Cleans up any stalled deployments in the database.
     *
     * @return void
     */
    public function cleanupDeployments()
    {
        // Mark any pending steps as cancelled
        ServerLog::where('status', '=', ServerLog::PENDING)
                 ->update(['status' => ServerLog::CANCELLED]);

        // Mark any running steps as failed
        ServerLog::where('status', '=', ServerLog::RUNNING)
                 ->update(['status' => ServerLog::FAILED]);

        // Mark any running deployments as failed
        Deployment::where('status', '=', Deployment::DEPLOYING)
                  ->update(['status' => Deployment::FAILED]);

        // TODO: Maybe readd to the queue if possible?
        Deployment::where('status', '=', Deployment::PENDING)
                  ->update(['status' => Deployment::FAILED]);

        // Mark any deploying projects as failed
        Project::where('status', '=', Project::DEPLOYING)
               ->update(['status' => Project::FAILED]);
    }
}
