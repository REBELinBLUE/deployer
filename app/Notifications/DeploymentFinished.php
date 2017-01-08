<?php

namespace REBELinBLUE\Deployer\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\NotifyEmail;
use REBELinBLUE\Deployer\Project;

/**
 * Notification sent when a deployment finishes.
 */
class DeploymentFinished extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, Deployment $deployment)
    {
        $this->project    = $project;
        $this->deployment = $deployment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  NotifyEmail                                    $email
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(NotifyEmail $email)
    {
        $table = [
            Lang::get('emails.project_name')    => $this->project->name,
            Lang::get('emails.deployed_branch') => $this->deployment->branch,
            Lang::get('emails.started_at')      => $this->deployment->started_at,
            Lang::get('emails.finished_at')     => $this->deployment->finished_at,
            Lang::get('emails.last_committer')  => $this->deployment->committer,
            Lang::get('emails.last_commit')     => $this->deployment->short_commit,
        ];

        $email = (new MailMessage)
            ->view(['notifications.email', 'notifications.email-plain'], [
                'name'  => $email->name,
                'table' => $table,
            ])
            ->subject(Lang::get('emails.deployment_done'))
            ->line(Lang::get('emails.deployment_header'))
            ->action(Lang::get('emails.deployment_details'), route('deployments', ['id' => $this->deployment->id]));

        if (!empty($this->deployment->reason)) {
            $email->line(Lang::get('emails.reason', ['reason' => $this->deployment->reason]));
        }

        return $email;
    }
}
