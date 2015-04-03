<?php namespace App;

use Lang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deployment extends Model
{
    use SoftDeletes;
    
    public function getDates()
    {
        return ['created_at', 'started_at', 'finished_at', 'updated_at'];
    }
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function isRunning()
    {
        return ($this->status == 'Deploying');
    }

    public function steps()
    {
        return $this->hasMany('App\DeployStep');
    }

    public function runtime()
    {
        if (!$this->finished_at) {
            return false;
        }

        return $this->started_at->diffInSeconds($this->finished_at);
    }

    public function commitURL()
    {
        if ($this->commit != 'Loading') {
            $info = $this->project->accessDetails();
            if (isset($info['domain']) && isset($info['reference'])) {
                return 'http://' . $info['domain'] . '/' . $info['reference'] . '/commit/' . $this->commit;
            }
        }

        return false;
    }

    public function shortCommit()
    {
        if ($this->commit != 'Loading') {
            return substr($this->commit, 0, 7);
        }

        return $this->commit;
    }

    public function notificationPayload()
    {
        $colour = 'good';
        $message = Lang::get('notification.success_message');

        if ($this->status === 'Failed') {
            $colour = 'danger';
            $message = Lang::get('notification.failed_message');
        }

        $payload = [
            'attachments' => [
                [
                    'fallback' => sprintf($message, '#' . $this->id),
                    'text'     => sprintf($message, sprintf('<%s|#%u>', url('deployment', $this->id), $this->id)),
                    'color'    => $colour,
                    'fields'   => [
                        [
                            'title' => Lang::get('notifications.project'),
                            'value' => sprintf('<%s|%s>', url('project', $this->project_id), $this->project->name),
                            'short' => true
                        ], [
                            'title' => Lang::get('notifications.commit'),
                            'value' => sprintf('<%s|%s>', $this->commitURL(), $this->shortCommit()),
                            'short' => true
                        ], [
                            'title' => Lang::get('notifications.committer'),
                            'value' => $this->committer,
                            'short' => true
                        ], [
                            'title' => Lang::get('notifications.branch'),
                            'value' => $this->project->branch,
                            'short' => true
                        ]
                    ]
                ]
            ]
        ];

        return $payload;
    }
}
