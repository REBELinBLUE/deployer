<?php namespace App\Commands;

use Mail;
use Lang;
use App\Commands\Command;
use App\Project;
use App\Deployment;

use Illuminate\Contracts\Bus\SelfHandling;

/**
 * Send email notifications for deployment
 */
class MailDeployNotification extends Command implements SelfHandling
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

        if ($emails) {
            $status = strtolower($this->project->getPresenter()->readable_status);
            $subject = Lang::get(
                'notifyEmails.subject',
                ['status'=>$status,'project'=>$this->project->name]
            );
            $projectArr = $this->project->toArray();
            $deploymentArr = $this->deployment->toArray();
            $deploymentArr['commitURL'] = $this->deployment->commitURL();
            $deploymentArr['shortCommit'] = $this->deployment->shortCommit();
            Mail::queue(
                'emails.deployed',
                ['project'=>$projectArr,'deployment'=>$deploymentArr],
                function ($message) use ($emails, $subject) {
                    foreach ($emails as $email) {
                        $message->to($email->email, $email->name);
                    }
                    $message->subject($subject);
                }
            );
        }
    }
}
