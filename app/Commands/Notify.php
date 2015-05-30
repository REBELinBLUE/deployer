<?php namespace App\Commands;

use App\Commands\Command;
use App\Notification;
use Httpful\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

/**
 * Sends notification to slack
 */
class Notify extends Command implements SelfHandling, ShouldBeQueued
{
    use InteractsWithQueue, SerializesModels;

    private $payload;
    private $notification;

    /**
     * Create a new command instance.
     *
     * @param Notification $notification
     * @param array $payload
     * @return Notify
     */
    public function __construct(Notification $notification, array $payload)
    {
        $this->notification = $notification;
        $this->payload = $payload;
    }

    /**
     * Execute the command.
     *
     * @return void
     * @todo Should use the supplied name
     */
    public function handle()
    {
        $payload = [
            'channel' => $this->notification->channel
        ];

        if (!empty($this->notification->icon)) {
            $icon_field = 'icon_url';
            if (preg_match('/:(.*):/', $this->notification->icon)) {
                $icon_field = 'icon_emoji';
            }

            $payload[$icon_field] = $this->notification->icon;
        }

        $payload = array_merge($payload, $this->payload);

        Request::post($this->notification->webhook)
               ->sendsJson()
               ->body($payload)
               ->send();
    }
}
