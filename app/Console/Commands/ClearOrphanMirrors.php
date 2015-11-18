<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;

/**
 * Checks for and cleans up orphaned git mirrors
 */
class ClearOrphanMirrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployer:purge-mirrors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purges git mirrors which are no longer in use by projects';

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
