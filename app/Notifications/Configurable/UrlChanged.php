<?php

namespace REBELinBLUE\Deployer\Notifications\Configurable;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Support\Arr;
use NotificationChannels\Twilio\TwilioSmsMessage as TwilioMessage;
use NotificationChannels\Webhook\WebhookMessage;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Notifications\Notification;

/**
 * Base class for URL notifications.
 */
abstract class UrlChanged extends Notification
{
    /**
     * @var CheckUrl
     */
    protected $url;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * Create a new notification instance.
     *
     * @param CheckUrl   $url
     * @param Translator $translator
     */
    public function __construct(CheckUrl $url, Translator $translator)
    {
        $this->url        = $url;
        $this->translator = $translator;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param string  $subject
     * @param string  $translation
     * @param Channel $notification
     *
     * @return MailMessage
     */
    protected function buildMailMessage($subject, $translation, Channel $notification)
    {
        $message = $this->translator->get($translation, ['link' => $this->url->name]);

        if (is_null($this->url->last_seen)) {
            $last_seen = $this->translator->get('app.never');
        } else {
            $last_seen = $this->url->last_seen->diffForHumans();
        }

        $table = [
            $this->translator->get('notifications.project_name') => $this->url->project->name,
            $this->translator->get('heartbeats.last_check_in')   => $last_seen,
            $this->translator->get('checkUrls.url')              => $this->url->url,
        ];

        $action = route('projects', ['id' => $this->url->project_id]);

        return (new MailMessage())
            ->view(['notifications.email', 'notifications.email-plain'], [
                'name'  => $notification->name,
                'table' => $table,
            ])
            ->subject($this->translator->get($subject))
            ->line($message)
            ->action($this->translator->get('notifications.project_details'), $action);
    }

    /**
     * Get the slack version of the notification.
     *
     * @param string  $translation
     * @param Channel $notification
     *
     * @return SlackMessage
     */
    protected function buildSlackMessage($translation, Channel $notification)
    {
        $message = $this->translator->get($translation, ['link' => $this->url->name]);
        $url     = route('projects', ['id' => $this->url->project_id]);

        if (is_null($this->url->last_seen)) {
            $last_seen = $this->translator->get('app.never');
        } else {
            $last_seen = $this->url->last_seen->diffForHumans();
        }

        $fields = [
            $this->translator->get('notifications.project') => sprintf('<%s|%s>', $url, $this->url->project->name),
            $this->translator->get('checkUrls.last_seen')   => $last_seen,
            $this->translator->get('checkUrls.url')         => $this->url->url,
        ];

        return (new SlackMessage())
            ->from(null, $notification->config->icon)
            ->to($notification->config->channel)
            ->attachment(function (SlackAttachment $attachment) use ($message, $fields) {
                $attachment
                    ->content($message)
                    ->fallback($message)
                    ->fields($fields)
                    ->footer($this->translator->get('app.name'))
                    ->timestamp($this->url->updated_at);
            });
    }

    /**
     * Get the webhook version of the notification.
     *
     * @param string  $event
     * @param Channel $notification
     *
     * @return WebhookMessage
     */
    protected function buildWebhookMessage($event, Channel $notification)
    {
        return (new WebhookMessage())
            ->data(array_merge(Arr::only(
                $this->url->attributesToArray(),
                ['id', 'name', 'missed', 'last_seen']
            ), [
                'status' => ($event === 'link_recovered') ? 'healthy' : 'missing',
            ]))
            ->header('X-Deployer-Project-Id', $notification->project_id)
            ->header('X-Deployer-Notification-Id', $notification->id)
            ->header('X-Deployer-Event', $event);
    }

    /**
     * Get the Twilio version of the notification.
     *
     * @param string $translation
     *
     * @return TwilioMessage
     */
    protected function buildTwilioMessage(string $translation): TwilioMessage
    {
        if (is_null($this->url->last_seen)) {
            $last_seen = $this->translator->get('app.never');
        } else {
            $last_seen = $this->url->last_seen->diffForHumans();
        }

        return (new TwilioMessage())
            ->content($this->translator->get($translation, [
                'link'    => $this->url->name,
                'project' => $this->url->project->name,
                'last'    => $last_seen,
            ]));
    }
}
