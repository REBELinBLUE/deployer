<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

use Httpful\Request;

use App\Notification;

class Notify extends Command implements SelfHandling, ShouldBeQueued
{
    use InteractsWithQueue, SerializesModels;

    private $payload;
    private $notification;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Notification $notification, $payload)
    {
        $this->notification = $notification;
        $this->payload = $payload;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $payload = [
            'channel' => $this->notification->channel
        ];

        $payload = array_merge($payload, $this->payload);

        Request::post($this->notification->webhook)
               ->sendsJson()
               ->body($payload)
               ->send();
    }
}
