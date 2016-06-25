<?php

namespace REBELinBLUE\Deployer\Jobs;

use Carbon\Carbon;
use Httpful\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Notification;

/**
 * Sends notification to slack.
 */
class SlackNotify extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var array
     */
    private $payload;

    /**
     * @var Notification
     */
    private $notification;

    /**
     * @var int
     */
    private $timeout;

    /**
     * SlackNotify constructor.
     *
     * @param Notification $notification
     * @param array $payload
     * @param int $timeout
     */
    public function __construct(Notification $notification, array $payload, $timeout = 60)
    {
        $this->notification = $notification;
        $this->payload      = $payload;
        $this->timeout      = $timeout;
    }

    /**
     * Execute the command.
     */
    public function handle()
    {
        $payload = [
            'channel' => $this->notification->channel,
        ];

        if (!empty($this->notification->icon)) {
            $icon_field = 'icon_url';
            if (preg_match('/:(.*):/', $this->notification->icon)) {
                $icon_field = 'icon_emoji';
            }

            $payload[$icon_field] = $this->notification->icon;
        }

        $payload = array_merge($payload, $this->payload);

        if (isset($payload['attachments'])) {
            $expire_at = Carbon::createFromTimestamp($payload['attachments'][0]['ts'])->addMinutes($this->timeout);

            if (Carbon::now()->gt($expire_at)) {
                return;
            }
        }

        Request::post($this->notification->webhook)
               ->sendsJson()
               ->body($payload)
               ->send();
    }
}
