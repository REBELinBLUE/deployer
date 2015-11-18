<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;

/**
 * Updates the mirrors for all git repositories
 */
class UpdateGitMirrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployer:update-mirrors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls in updates for git mirrors';

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
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
