<?php

namespace REBELinBLUE\Deployer\Jobs;

use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

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
        $this->links->each(function ($link) use ($client) {
            try {
                $client->get($link->url);

                $link->online();
            } catch (\Exception $error) {
                $link->offline();
            }
        });
    }
}
