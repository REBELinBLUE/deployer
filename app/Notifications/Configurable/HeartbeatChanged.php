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
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Notifications\Notification;

/**
 * Base class for Heartbeat notifications.
 */
abstract class HeartbeatChanged extends Notification
{
    /**
     * @var Heartbeat
     */
    protected $heartbeat;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * Create a new notification instance.
     *
     * @param Heartbeat  $heartbeat
     * @param Translator $translator
     */
    public function __construct(Heartbeat $heartbeat, Translator $translator)
    {
        $this->heartbeat  = $heartbeat;
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
    protected function buildMailMessage(string $subject, string $translation, Channel $notification): MailMessage
    {
        $message = $this->translator->get($translation, ['job' => $this->heartbeat->name]);

        if (is_null($this->heartbeat->last_activity)) {
            $heard_from = $this->translator->get('app.never');
        } else {
            $heard_from = $this->heartbeat->last_activity->diffForHumans();
        }

        $table = [
            $this->translator->get('notifications.project_name') => $this->heartbeat->project->name,
            $this->translator->get('heartbeats.last_check_in')   => $heard_from,
        ];

        $action = route('projects', ['id' => $this->heartbeat->project_id]);

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
    protected function buildSlackMessage(string $translation, Channel $notification): SlackMessage
    {
        $message = $this->translator->get($translation, ['job' => $this->heartbeat->name]);
        $url     = route('projects', ['id' => $this->heartbeat->project_id]);

        if (is_null($this->heartbeat->last_activity)) {
            $heard_from = $this->translator->get('app.never');
        } else {
            $heard_from = $this->heartbeat->last_activity->diffForHumans();
        }

        $fields = [
            $this->translator->get('notifications.project') => sprintf(
                '<%s|%s>',
                $url,
                $this->heartbeat->project->name
            ),
            $this->translator->get('heartbeats.last_check_in') => $heard_from,
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
                    ->timestamp($this->heartbeat->updated_at);
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
    protected function buildWebhookMessage(string $event, Channel $notification): WebhookMessage
    {
        return (new WebhookMessage())
            ->data(array_merge(Arr::only(
                $this->heartbeat->attributesToArray(),
                ['id', 'name', 'missed', 'last_activity']
            ), [
                'status' => ($event === 'heartbeat_recovered') ? 'healthy' : 'missing',
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
        if (is_null($this->heartbeat->last_activity)) {
            $heard_from = $this->translator->get('app.never');
        } else {
            $heard_from = $this->heartbeat->last_activity->diffForHumans();
        }

        return (new TwilioMessage())
            ->content($this->translator->get($translation, [
                'job'     => $this->heartbeat->name,
                'project' => $this->heartbeat->project->name,
                'last'    => $heard_from,
            ]));
    }
}
