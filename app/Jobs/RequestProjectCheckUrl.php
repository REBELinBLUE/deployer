<?php namespace App\Jobs;

use Queue;
use App\Jobs\Job;
use Httpful\Request;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Request the urls
 */
class RequestProjectCheckUrl extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $link;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($link)
    {
        $this->link = $link;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $response = Request::get($this->link->url)->send();

        $this->link->last_status = $response->hasErrors();
        $this->link->save();

        if ($response->hasErrors()) {
            foreach ($this->link->project->notifications as $notification) {
                Queue::push(new Notify(
                    $notification,
                    $this->link->notificationPayload()
                ));
            }
        }
    }
}
