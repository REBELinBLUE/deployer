<?php

namespace REBELinBLUE\Deployer\Jobs;

use Httpful\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Jobs\Job;
use REBELinBLUE\Deployer\Notification;
use REBELinBLUE\Deployer\Message;
use REBELinBLUE\Deployer\Decorators\SlackMessage;
use REBELinBLUE\Deployer\Decorators\HipchatMessage;

/**
 * Sends notification to slack.
 */
class Notify extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $notification;
    private $decorator;

    /**
     * Create a new command instance.
     *
     * @param  Notification $notification
     * @param  Message      $message
     * @return Notify
     */
    public function __construct(Notification $notification, Message $message)
    {
        $this->notification = $notification;
        $this->decorator = $this->getDecorator($notification, $message);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        Request::post($this->notification->webhook)
               ->sendsJson()
               ->body($this->decorator->getPayload())
               ->send();
    }

    public function getDecorator(Notification $notification, Message $message)
    {
        $class = HipchatMessage::class;
        if ($notification->service === Notification::SLACK) {
            $class = SlackMessage::class;
        }

        return new $class($notification, $message);
    }
}
