<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Events\Contracts\HasSlackPayloadInterface;
use REBELinBLUE\Deployer\Events\Event;
use REBELinBLUE\Deployer\Heartbeat;

/**
 * Event class which is thrown when the heartbeat recovers.
 **/
class HeartbeatMissed extends Event implements HasSlackPayloadInterface
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
     * Generates a slack payload for the heartbeat failure.
     *
     * @return array
     */
    public function notificationPayload()
    {
        $message = Lang::get('heartbeats.missing_message', ['job' => $this->heartbeat->name]);
        $url     = url('projects', $this->heartbeat->project_id);

        if (is_null($this->heartbeat->last_activity)) {
            $heard_from = Lang::get('app.never');
        } else {
            $heard_from = $this->heartbeat->last_activity->diffForHumans();
        }

        $payload = [
            'attachments' => [
                [
                    'fallback' => $message,
                    'text'     => $message,
                    'color'    => 'danger',
                    'fields'   => [
                        [
                            'title' => Lang::get('notifications.project'),
                            'value' => sprintf('<%s|%s>', $url, $this->heartbeat->project->name),
                            'short' => true,
                        ], [
                            'title' => Lang::get('heartbeats.last_check_in'),
                            'value' => $heard_from,
                            'short' => true,
                        ],
                    ],
                ],
            ],
        ];

        return $payload;
    }
}
