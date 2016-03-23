<?php

namespace REBELinBLUE\Deployer\Jobs;

use Httpful\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Jobs\Job;
use REBELinBLUE\Deployer\Message;
use REBELinBLUE\Deployer\Notification;
use REBELinBLUE\Deployer\Messages\Translators\HipchatMessage;
use REBELinBLUE\Deployer\Messages\Translators\SlackMessage;
use REBELinBLUE\Deployer\Messages\Contracts\MessageInterface;

/**
 * Sends notification to slack.
 */
class Notify extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $webhook;
    private $translator;

    /**
     * Create a new command instance.
     *
     * @param  Notification $notification
     * @param  Message      $message
     * @return Notify
     */
    public function __construct(Notification $notification, MessageInterface $message)
    {
        $this->webhook    = $notification->webhook;
        $this->translator = $this->getTranslator($notification, $message);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        Request::post($this->webhook)
               ->sendsJson()
               ->body($this->translator->getPayload())
               ->send();
    }

    /**
     * Get the translator class for the notification.
     *
     * @param  Notification         $notification
     * @param  MessageInterface              $message
     * @return ChatMessageInterface
     */
    private function getTranslator(Notification $notification, MessageInterface $message)
    {
        $class = HipchatMessage::class;
        if ($notification->isSlack()) {
            $class = SlackMessage::class;
        }

        return new $class($notification, $message);
    }
}
