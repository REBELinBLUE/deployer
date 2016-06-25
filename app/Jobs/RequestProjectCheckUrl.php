<?php

namespace REBELinBLUE\Deployer\Jobs;

use Httpful\Exception\ConnectionErrorException;
use Httpful\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\CheckUrl;

/**
 * Request the urls.
 */
class RequestProjectCheckUrl extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $links;

    /**
     * RequestProjectCheckUrl constructor.
     *
     * @param CheckUrl[] $links
     */
    public function __construct($links)
    {
        $this->links = $links;
    }

    /**
     * Execute the command.
     * @dispatches SlackNotify
     */
    public function handle()
    {
        foreach ($this->links as $link) {
            try {
                $response = Request::get($link->url)->send();

                $has_error = $response->hasErrors();
            } catch (ConnectionErrorException $error) {
                $has_error = true;
            }

            $link->last_status = $has_error;
            $link->save();

            if ($has_error) {
                foreach ($link->project->notifications as $notification) {
                    try {
                        $this->dispatch(new SlackNotify($notification, $link->notificationPayload()));
                    } catch (\Exception $error) {
                        // Don't worry about this error
                    }
                }
            }
        }
    }
}
