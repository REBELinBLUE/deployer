<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Jobs\RequestProjectCheckUrl;
use REBELinBLUE\Deployer\Repositories\Contracts\CheckUrlRepositoryInterface;

/**
 * Schedule the url check.
 */
class CheckUrls extends Command
{
    use DispatchesJobs;

    const URLS_TO_CHECK = 10;

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
     * @var CheckUrlRepositoryInterface
     */
    private $repository;

    /**
     * CheckUrls constructor.
     *
     * @param CheckUrlRepositoryInterface $repository
     */
    public function __construct(CheckUrlRepositoryInterface $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        $period = [];

        $minute = (int) (Carbon::now()->format('i'));
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

        if (count($period)) {
            $this->repository->chunkWhereIn('period', $period, self::URLS_TO_CHECK, function (Collection $urls) {
                $this->dispatch(new RequestProjectCheckUrl($urls));
            });
        }
    }
}
