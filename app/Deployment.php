<?php namespace App;

use Lang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Deployment model
 */
class Deployment extends Model
{
    use SoftDeletes;

    const COMPLETED = 0;
    const PENDING   = 1;
    const DEPLOYING = 2;
    const FAILED    = 3;
    const LOADING   = 'Loading';

    /**
     * Overwrite Laravel's getDate() function to add additional dates
     *
     * @return array
     */
    public function getDates()
    {
        return ['created_at', 'started_at', 'finished_at', 'updated_at'];
    }

    /**
     * Belongs to relationship
     *
     * @return Project
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * Belongs to relationship
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Has many relationship
     *
     * @return DeployStep
     */
    public function steps()
    {
        return $this->hasMany('App\DeployStep');
    }

    /**
     * Determines whether the deployment is running
     *
     * @return boolean
     */
    public function isRunning()
    {
        return ($this->status == self::DEPLOYING);
    }

    /**
     * Determines how long the deploy took
     *
     * @return false|int False if the deploy is still running, otherwise the runtime in seconds
     */
    public function runtime()
    {
        if (!$this->finished_at) {
            return false;
        }

        return $this->started_at->diffInSeconds($this->finished_at);
    }

    /**
     * Gets the HTTP URL to the commit
     *
     * @return string|false
     */
    public function commitURL()
    {
        if ($this->commit != self::LOADING) {
            $info = $this->project->accessDetails();
            if (isset($info['domain']) && isset($info['reference'])) {
                return 'http://' . $info['domain'] . '/' . $info['reference'] . '/commit/' . $this->commit;
            }
        }

        return false;
    }

    /**
     * Gets the short commit hash
     *
     * @return string
     */
    public function shortCommit()
    {
        if ($this->commit != self::LOADING) {
            return substr($this->commit, 0, 7);
        }

        return $this->commit;
    }

    /**
     * Generates a slack payload for the deployment
     *
     * @return array
     */
    public function notificationPayload()
    {
        $colour = 'good';
        $message = Lang::get('notifications.success_message');

        if ($this->status === self::FAILED) {
            $colour = 'danger';
            $message = Lang::get('notifications.failed_message');
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
                            'value' => $this->commitURL() ? sprintf('<%s|%s>', $this->commitURL(), $this->shortCommit()) : $this->shortCommit(),
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
