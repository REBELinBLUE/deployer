<?php

namespace REBELinBLUE\Deployer\Jobs;

use Httpful\Exception\ConnectionErrorException;
use Httpful\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use REBELinBLUE\Deployer\Jobs\Job;

/**
 * Request the urls.
 */
class RequestProjectCheckUrl extends Job implements ShouldQueue
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
        DB::reconnect();

        foreach ($this->links as $link) {
            $has_error = false;

            try {
                $response = Request::get($link->url)->send();

                $link->last_status = $response->hasErrors();
                $link->save();

                $has_error = $response->hasErrors();
            } catch (ConnectionErrorException $error) {
                $has_error = true;
            }

            $link->last_status = $has_error;
            $link->save();

            if ($has_error) {
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
