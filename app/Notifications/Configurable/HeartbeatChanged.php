<?php

namespace REBELinBLUE\Deployer\Notifications\Configurable;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Support\Facades\Lang;
use NotificationChannels\HipChat\Card;
use NotificationChannels\HipChat\CardAttribute;
use NotificationChannels\HipChat\CardFormats;
use NotificationChannels\HipChat\CardStyles;
use NotificationChannels\HipChat\HipChatMessage;
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
     * Create a new notification instance.
     */
    public function __construct(Heartbeat $heartbeat)
    {
        $this->heartbeat = $heartbeat;
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
        $message = Lang::get($translation, ['job' => $this->heartbeat->name]);

        if (is_null($this->heartbeat->last_activity)) {
            $heard_from = Lang::get('app.never');
        } else {
            $heard_from = $this->heartbeat->last_activity->diffForHumans();
        }

        $table = [
            Lang::get('notifications.project_name') => $this->heartbeat->project->name,
            Lang::get('heartbeats.last_check_in')   => $heard_from,
        ];

        $action = route('projects', ['id' => $this->heartbeat->project_id]);

        return (new MailMessage)
            ->view(['notifications.email', 'notifications.email-plain'], [
                'name'  => $notification->name,
                'table' => $table,
            ])
            ->to($notification->config->email)
            ->subject(Lang::get($subject))
            ->line($message)
            ->action(Lang::get('notifications.project_details'), $action);
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
        $message = Lang::get($translation, ['job' => $this->heartbeat->name]);
        $url     = route('projects', ['id' => $this->heartbeat->project_id]);

        if (is_null($this->heartbeat->last_activity)) {
            $heard_from = Lang::get('app.never');
        } else {
            $heard_from = $this->heartbeat->last_activity->diffForHumans();
        }

        $fields = [
            Lang::get('notifications.project')    => sprintf('<%s|%s>', $url, $this->heartbeat->project->name),
            Lang::get('heartbeats.last_check_in') => $heard_from,
        ];

        return (new SlackMessage)
            ->from(null, $notification->config->icon)
            ->to($notification->config->channel)
            ->attachment(function (SlackAttachment $attachment) use ($message, $fields) {
                $attachment
                    ->content($message)
                    ->fallback($message)
                    ->fields($fields)
                    ->footer(Lang::get('app.name'))
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
    protected function buildWebhookMessage($event, Channel $notification)
    {
        return (new WebhookMessage)
            ->data(array_merge(array_only(
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
    protected function buildTwilioMessage($translation)
    {
        if (is_null($this->heartbeat->last_activity)) {
            $heard_from = Lang::get('app.never');
        } else {
            $heard_from = $this->heartbeat->last_activity->diffForHumans();
        }

        return (new TwilioMessage)
            ->content(Lang::get($translation, [
                'job'     => $this->heartbeat->name,
                'project' => $this->heartbeat->project->name,
                'last'    => $heard_from,
            ]));
    }

    /**
     * Get the Hipchat version of the message.
     *
     * @param string  $translation
     * @param Channel $notification
     *
     * @return HipChatMessage
     */
    protected function buildHipchatMessage($translation, Channel $notification)
    {
        $message = Lang::get($translation, ['job' => $this->heartbeat->name]);
        $url     = route('projects', ['id' => $this->heartbeat->project_id]);

        return (new HipChatMessage)
            ->room($notification->config->room)
            ->notify()
            ->html($message)
            ->card(function (Card $card) use ($message, $url) {
                $card
                    ->title($message)
                    ->url($url)
                    ->style(CardStyles::APPLICATION)
                    ->cardFormat(CardFormats::MEDIUM)
                    ->addAttribute(function (CardAttribute $attribute) {
                        $attribute
                            ->label(Lang::get('notifications.project'))
                            ->value($this->heartbeat->project->name)
                            ->url(route('projects', ['id' => $this->heartbeat->project_id]));
                    })
                    ->addAttribute(function (CardAttribute $attribute) {
                        if (is_null($this->heartbeat->last_activity)) {
                            $heard_from = Lang::get('app.never');
                        } else {
                            $heard_from = $this->heartbeat->last_activity->diffForHumans();
                        }

                        $attribute
                            ->label(Lang::get('heartbeats.last_check_in'))
                            ->value($heard_from);
                    });
            });
    }
}
