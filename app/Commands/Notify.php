<?php namespace App\Commands;

use App\Commands\Command;

use App\Deployment;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

use Httpful\Request;

class Notify extends Command implements SelfHandling, ShouldBeQueued
{
    use InteractsWithQueue, SerializesModels;

    private $deployment;
    private $channel;
    private $webhook;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($webhook, $channel, Deployment $deployment)
    {
        $this->webhook = $webhook;
        $this->channel = $channel;
        $this->deployment = $deployment;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $colour = 'good';
        $message = 'Deployment %s successful!';

        if ($this->deployment->status === 'Failed') {
            $colour = 'danger';
            $message = 'Deployment %s failed!';
        }

        $payload = [
            'channel'     => $this->channel,
            'attachments' => [
                [
                    'fallback' => sprintf($message, '#' . $this->deployment->id),
                    'text'     => sprintf($message, sprintf('<%s|#%u>', url('deployment', $this->deployment->id), $this->deployment->id)),
                    'color'    => $colour,
                    'fields'   => [
                        [
                            'title' => 'Project',
                            'value' => sprintf('<%s|%s>', url('project', $this->deployment->project_id), $this->deployment->project->name),
                            'short' => true
                        ], [
                            'title' => 'Commit',
                            'value' => sprintf('<%s|%s>', $this->deployment->commitURL(), $this->deployment->shortCommit()),
                            'short' => true
                        ], [
                            'title' => 'Committer',
                            'value' => $this->deployment->committer,
                            'short' => true
                        ], [
                            'title' => 'Branch',
                            'value' => $this->deployment->project->branch,
                            'short' => true
                        ]
                    ]
                ]
            ]
        ];

        Request::post($this->webhook)
               ->sendsJson()
               ->body($payload)
               ->send();
    }
}
