<?php

namespace App\Console\Commands;

use App\CheckUrl as CheckUrlModel;
use App\Jobs\RequestProjectCheckUrl;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Schedule the url check.
 */
class CheckUrl extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployer:checkurls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Request the project check URL and notify when failed.';

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
        $period = [];

        $minute = intval(date('i'));
        if ($minute === 0) {
            $period = [60, 30, 10, 5];
        } else {
            if ($minute % 30 === 0) {
                $period = [30, 10, 5];
            } elseif ($minute % 10 === 0) {
                $period = [10, 5];
            } elseif ($minute % 5 === 0) {
                $period = [5];
            }
        }

        if (empty($period)) {
            return true;
        }

        $command = $this;

        CheckUrlModel::whereIn('period', $period)->chunk(10, function ($urls) use ($command) {

            $command->dispatch(new RequestProjectCheckUrl($urls));

        });
    }
}
