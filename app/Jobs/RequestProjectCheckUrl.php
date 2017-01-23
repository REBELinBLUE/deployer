<?php

namespace REBELinBLUE\Deployer\Jobs;

use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\CheckUrl;

/**
 * Request the urls.
 */
class RequestProjectCheckUrl extends Job implements ShouldQueue
{
    use SerializesModels;

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $links;

    /**
     * RequestProjectCheckUrl constructor.
     *
     * @param Collection $links
     */
    public function __construct(Collection $links)
    {
        $this->links = $links;
    }

    /**
     * Execute the command.
     */
    public function handle()
    {
        foreach ($this->links as $link) {
            try {
                app()->make(Client::class)->get($link->url);

                $link->online();
            } catch (\Exception $error) {
                $link->offline();
            }
        }
    }
}
