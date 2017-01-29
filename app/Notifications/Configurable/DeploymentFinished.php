<?php

namespace REBELinBLUE\Deployer\Notifications\Configurable;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Support\Facades\Lang;
use NotificationChannels\HipChat\Card;
use NotificationChannels\HipChat\CardAttribute;
use NotificationChannels\HipChat\CardAttributeStyles;
use NotificationChannels\HipChat\CardFormats;
use NotificationChannels\HipChat\CardStyles;
use NotificationChannels\HipChat\HipChatMessage;
use NotificationChannels\Twilio\TwilioSmsMessage as TwilioMessage;
use NotificationChannels\Webhook\WebhookMessage;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Notifications\Notification;
use REBELinBLUE\Deployer\Project;

/**
 * Base class for Deployment notifications.
 */
abstract class DeploymentFinished extends Notification
{
    /**
     * @var Project
     */
    protected $project;

    /**
     * @var Deployment
     */
    protected $deployment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, Deployment $deployment)
    {
        $this->project    = $project;
        $this->deployment = $deployment;
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
        $message = Lang::get($translation);

        $table = [
            Lang::get('notifications.project_name')    => $this->project->name,
            Lang::get('notifications.deployed_branch') => $this->deployment->branch,
            Lang::get('notifications.started_at')      => $this->deployment->started_at,
            Lang::get('notifications.finished_at')     => $this->deployment->finished_at,
            Lang::get('notifications.last_committer')  => $this->deployment->committer,
            Lang::get('notifications.last_commit')     => $this->deployment->short_commit,
        ];

        $action = route('deployments', ['id' => $this->deployment->id]);

        $email = (new MailMessage())
            ->view(['notifications.email', 'notifications.email-plain'], [
                'name'  => $notification->name,
                'table' => $table,
            ])
            ->subject(Lang::get($subject))
            ->line($message)
            ->action(Lang::get('notifications.deployment_details'), $action);

        if (!empty($this->deployment->reason)) {
            $email->line(Lang::get('notifications.reason', ['reason' => $this->deployment->reason]));
        }

        return $email;
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
        $message = Lang::get($translation);

        $fields = [
            Lang::get('notifications.project') => sprintf(
                '<%s|%s>',
                route('projects', ['id' => $this->project->id]),
                $this->project->name
            ),
            Lang::get('notifications.commit') => $this->deployment->commit_url ? sprintf(
                '<%s|%s>',
                $this->deployment->commit_url,
                $this->deployment->short_commit
            ) : $this->deployment->short_commit,
            Lang::get('notifications.committer') => $this->deployment->committer,
            Lang::get('notifications.branch')    => $this->deployment->branch,
        ];

        return (new SlackMessage())
            ->from(null, $notification->config->icon)
            ->to($notification->config->channel)
            ->attachment(function (SlackAttachment $attachment) use ($message, $fields) {
                $attachment
                    ->content(sprintf($message, sprintf(
                        '<%s|#%u>',
                        route('deployments', ['id' => $this->deployment->id]),
                        $this->deployment->id
                    )))
                    ->fallback(sprintf($message, '#' . $this->deployment->id))
                    ->fields($fields)
                    ->footer(Lang::get('app.name'))
                    ->timestamp($this->deployment->finished_at);
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
            ->data(array_merge(array_only(
                $this->deployment->attributesToArray(),
                ['id', 'branch', 'started_at', 'finished_at', 'commit', 'source', 'reason']
            ), [
                'project'      => $this->deployment->project_name,
                'committed_by' => $this->deployment->committer,
                'started_by'   => $this->deployment->deployer_name,
                'status'       => ($event === 'deployment_succeeded') ? 'success' : 'failure',
                'url'          => route('deployments', ['id' => $this->deployment->id]),
            ]))
            ->header('X-Deployer-Project-Id', $notification->project_id)
            ->header('X-Deployer-Notification-Id', $notification->id)
            ->header('X-Deployer-Event', $event);
    }

    /**
     * Gets the Twilio version of the notification.
     *
     * @param string $translation
     *
     * @return TwilioMessage
     */
    protected function buildTwilioMessage($translation)
    {
        return (new TwilioMessage())
            ->content(Lang::get($translation, [
                'id'      => $this->deployment->id,
                'project' => $this->project->name,
            ]));
    }

    /**
     * Gets the Hipchat version of the message.
     *
     * @param string  $translation
     * @param Channel $notification
     *
     * @return HipChatMessage
     */
    protected function buildHipchatMessage($translation, Channel $notification)
    {
        $message = Lang::get($translation);

        return (new HipChatMessage())
            ->room($notification->config->room)
            ->notify()
            ->html(sprintf($message, sprintf(
                '<a href="%s">#%u</a>',
                route('deployments', ['id' => $this->deployment->id]),
                $this->deployment->id
            )))
            ->card(function (Card $card) use ($message) {
                $card
                    ->title(sprintf($message, '#' . $this->deployment->id))
                    ->url(route('deployments', ['id' => $this->deployment->id]))
                    ->style(CardStyles::APPLICATION)
                    ->cardFormat(CardFormats::MEDIUM)
                    ->addAttribute(function (CardAttribute $attribute) {
                        $attribute
                            ->label(Lang::get('notifications.project'))
                            ->value($this->project->name)
                            ->url(route('projects', ['id' => $this->project->id]));
                    })
                    ->addAttribute(function (CardAttribute $attribute) {
                        $attribute
                            ->label(Lang::get('notifications.commit'))
                            ->value($this->deployment->short_commit)
                            ->url($this->deployment->commit_url);
                    })
                    ->addAttribute(function (CardAttribute $attribute) {
                        $attribute
                            ->label(Lang::get('notifications.committer'))
                            ->value($this->deployment->committer);
                    })
                    ->addAttribute(function (CardAttribute $attribute) {
                        $attribute
                            ->label(Lang::get('notifications.branch'))
                            ->style(CardAttributeStyles::GENERAL)
                            ->value($this->deployment->branch);
                    });
            });
    }
}
