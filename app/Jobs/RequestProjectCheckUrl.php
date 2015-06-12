<?php

namespace App\Jobs;

use App\Jobs\Job;
use Httpful\Request;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Request the urls.
 */
class RequestProjectCheckUrl extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    private $links;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($links)
    {
        $this->links = $links;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->links as $link) {
            $response = Request::get($link->url)->send();

            $link->last_status = $response->hasErrors();
            $link->save();

            if ($response->hasErrors()) {
                foreach ($link->project->notifications as $notification) {
                    $this->dispatch(new Notify(
                        $notification,
                        $link->notificationPayload()
                    ));
                }
            }
        }
    }
}
