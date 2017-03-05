<?php

namespace REBELinBLUE\Deployer\Events\Listeners;

use Illuminate\Contracts\Translation\Translator;
use REBELinBLUE\Deployer\Events\UrlChanged;
use REBELinBLUE\Deployer\Notifications\Configurable\UrlDown;
use REBELinBLUE\Deployer\Notifications\Configurable\UrlRecovered;

/**
 * Event handler class for URL notifications.
 **/
class SendCheckUrlNotifications
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Handle the event.
     *
     * @param UrlChanged $event
     */
    public function handle(UrlChanged $event)
    {
        $link = $event->url;

        $notification = UrlRecovered::class;
        $event        = 'link_recovered';

        if (!$link->isHealthy()) {
            $notification = UrlDown::class;
            $event        = 'link_down';

            if ($link->missed > 1) {
                $event = 'link_still_down';
            }
        }

        foreach ($link->project->channels->where('on_' . $event, true) as $channel) {
            $channel->notify(new $notification($link, $this->translator));
        }
    }
}
