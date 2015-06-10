<?php namespace App\Jobs;

use Mail;
use Lang;
use App\Jobs\Job;
use App\Project;
use App\Deployment;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Mail\Message;

/**
 * Send email notifications for deployment
 */
class MailDeployNotification extends Job implements SelfHandling
{
    private $project;
    private $deployment;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Project $project, Deployment $deployment)
    {
        $this->project = $project;
        $this->deployment = $deployment;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $emails = $this->project->notifyEmails;

        if ($emails->count() > 0) {
            $status = strtolower($this->project->getPresenter()->readable_status);

            $subject = Lang::get(
                'notifyEmails.subject',
                ['status' => $status, 'project' => $this->project->name]
            );

            $deploymentArr = $this->deployment->toArray();
            $deploymentArr['commitURL'] = $this->deployment->commitURL();
            $deploymentArr['shortCommit'] = $this->deployment->shortCommit();

            $data = [
                'project'    => $this->project->toArray(),
                'deployment' => $deploymentArr
            ];

            Mail::queueOn(
                'low',
                'emails.deployed',
                $data,
                function (Message $message) use ($emails, $subject) {
                    foreach ($emails as $email) {
                        $message->to($email->email, $email->name);
                    }

                    $message->subject($subject);
                }
            );
        }
    }
}
