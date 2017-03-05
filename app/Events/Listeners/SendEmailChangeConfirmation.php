<?php

namespace REBELinBLUE\Deployer\Events\Listeners;

use Illuminate\Contracts\Translation\Translator;
use REBELinBLUE\Deployer\Events\EmailChangeRequested;
use REBELinBLUE\Deployer\Notifications\System\ChangeEmail;

/**
 * Request email change handler.
 */
class SendEmailChangeConfirmation
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
     * @param EmailChangeRequested $event
     */
    public function handle(EmailChangeRequested $event)
    {
        $token = $event->user->requestEmailToken();

        $event->user->notify(new ChangeEmail($token, $this->translator));
    }
}
