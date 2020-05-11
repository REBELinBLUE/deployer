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
     * @var Translator
     */
    protected $translator;

    /**
     * Create a new notification instance.
     *
     * @param Project    $project
     * @param Deployment $deployment
     * @param Translator $translator
     */
    public function __construct(Project $project, Deployment $deployment, Translator $translator)
    {
        $this->project    = $project;
        $this->deployment = $deployment;
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
        $message = $this->translator->get($translation);

        $table = [
            $this->translator->get('notifications.project_name')    => $this->project->name,
            $this->translator->get('notifications.deployed_branch') => $this->deployment->branch,
            $this->translator->get('notifications.started_at')      => $this->deployment->started_at,
            $this->translator->get('notifications.finished_at')     => $this->deployment->finished_at,
            $this->translator->get('notifications.last_committer')  => $this->deployment->committer,
            $this->translator->get('notifications.last_commit')     => $this->deployment->short_commit,
        ];

        $action = route('deployments', ['id' => $this->deployment->id]);

        $email = (new MailMessage())
            ->view(['notifications.email', 'notifications.email-plain'], [
                'name'  => $notification->name,
                'table' => $table,
            ])
            ->subject($this->translator->get($subject))
            ->line($message)
            ->action($this->translator->get('notifications.deployment_details'), $action);

        if (!empty($this->deployment->reason)) {
            $email->line($this->translator->get('notifications.reason', ['reason' => $this->deployment->reason]));
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
    protected function buildSlackMessage(string $translation, Channel $notification): SlackMessage
    {
        $message = $this->translator->get($translation);

        $fields = [
            $this->translator->get('notifications.project') => sprintf(
                '<%s|%s>',
                route('projects', ['id' => $this->project->id]),
                $this->project->name
            ),
            $this->translator->get('notifications.commit') => $this->deployment->commit_url ? sprintf(
                '<%s|%s>',
                $this->deployment->commit_url,
                $this->deployment->short_commit
            ) : $this->deployment->short_commit,
            $this->translator->get('notifications.committer') => $this->deployment->committer,
            $this->translator->get('notifications.branch')    => $this->deployment->branch,
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
                    ->footer($this->translator->get('app.name'))
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
    protected function buildWebhookMessage(string $event, Channel $notification): WebhookMessage
    {
        return (new WebhookMessage())
            ->data(array_merge(Arr::only(
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
    protected function buildTwilioMessage($translation): TwilioMessage
    {
        return (new TwilioMessage())
            ->content($this->translator->get($translation, [
                'id'      => $this->deployment->id,
                'project' => $this->project->name,
            ]));
    }
}
