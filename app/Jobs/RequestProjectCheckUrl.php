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
     *
     * @param Client $client
     */
    public function handle(Client $client)
    {
        $this->links->each(function (CheckUrl $link) use ($client) {
            try {
                $client->get($link->url);

                $link->online();
            } catch (\Exception $error) { // FIXME: Change te exception
                $link->offline();
            }
        });
    }
}
