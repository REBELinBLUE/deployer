<?php

namespace REBELinBLUE\Deployer\Events\Observers;

use Illuminate\Contracts\Translation\Translator;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Notifications\System\NewTestNotification;

/**
 * Event observer for Channel model.
 */
class ChannelObserver
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
     * Called when the model is saved.
     *
     * @param Channel $channel
     */
    public function saved(Channel $channel)
    {
        $channel->notify(new NewTestNotification($this->translator));
    }
}
