<?php namespace App\Jobs;

use App\Jobs\Job;
use Httpful\Request;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\DispatchesCommands;

/**
 * Request the urls
 */
class RequestProjectCheckUrl extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesCommands;

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
     * Overwrite the queue method to push to a different queue
     * 
     * @param Queue $queue
     * @param RequestProjectCheckUrl $command
     * @return void
     */
    public function queue($queue, RequestProjectCheckUrl $command)
    {
        $queue->pushOn('low', $command);
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
