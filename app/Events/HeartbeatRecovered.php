<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Contracts\Events\HasSlackPayloadInterface;
use REBELinBLUE\Deployer\Events\Event;
use REBELinBLUE\Deployer\Heartbeat;

/**
 * Event class which is thrown when the heartbeat recovers.
 **/
class HeartbeatRecovered extends Event implements HasSlackPayloadInterface
{
    use SerializesModels;

    public $heartbeat;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Heartbeat $heartbeat)
    {
        $this->heartbeat = $heartbeat;
    }

    /**
     * Generates a slack payload for the heartbeat recovery.
     *
     * @return array
     */
    public function notificationPayload()
    {
        $message = Lang::get('heartbeats.recovered_message', ['job' => $this->heartbeat->name]);
        $url     = route('projects', ['id' => $this->heartbeat->project_id]);

        $payload = [
            'attachments' => [
                [
                    'fallback' => $message,
                    'text'     => $message,
                    'color'    => 'good',
                    'fields'   => [
                        [
                            'title' => Lang::get('notifications.project'),
                            'value' => sprintf('<%s|%s>', $url, $this->heartbeat->project->name),
                            'short' => true,
                        ],
                    ],
                    'footer' => Lang::get('app.name'),
                    'ts'     => time(),
                ],
            ],
        ];

        return $payload;
    }
}
