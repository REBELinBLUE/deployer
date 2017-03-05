<?php

namespace REBELinBLUE\Deployer\Events\Listeners;

use Illuminate\Contracts\Translation\Translator;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Notifications\System\NewAccount;

/**
 * Sends an email when the user has been created.
 */
class SendSignupEmail
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
     * @param UserWasCreated $event
     */
    public function handle(UserWasCreated $event)
    {
        $event->user->notify(new NewAccount($event->password, $this->translator));
    }
}
