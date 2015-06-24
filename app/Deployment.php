<?php

namespace App;

use App\Contracts\RuntimeInterface;
use App\Events\ModelChanged;
use App\Presenters\DeploymentPresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;
use Robbo\Presenter\PresentableInterface;

/**
 * Deployment model.
 */
class Deployment extends Model implements PresentableInterface, RuntimeInterface
{
    use SoftDeletes;

    const COMPLETED = 0;
    const PENDING   = 1;
    const DEPLOYING = 2;
    const FAILED    = 3;
    const LOADING   = 'Loading';

    public static $currentDeployment = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['reason', 'branch', 'project_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'deleted_at', 'updated_at', 'user'];

    /**
     * Additional attributes to include in the JSON representation.
     *
     * @var array
     */
    protected $appends = ['project_name', 'deployer_name', 'commit_url',
                          'short_commit', 'branch_url', 'repo_failure',];

    /**
     * The fields which should be tried as Carbon instances.
     *
     * @var array
     */
    protected $dates = ['started_at', 'finished_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * Override the boot method to bind model event listeners.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saved(function (Deployment $model) {
            event(new ModelChanged($model, 'deployment'));
        });
    }

    /**
     * Belongs to relationship.
     *
     * @return Project
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * Belongs to relationship.
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Has many relationship.
     *
     * @return DeployStep
     */
    public function steps()
    {
        return $this->hasMany('App\DeployStep');
    }

    /**
     * Determines whether the deployment is running.
     *
     * @return bool
     */
    public function isRunning()
    {
        return ($this->status === self::DEPLOYING);
    }

    /**
     * Determines whether the deployment is successful.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return ($this->status === self::COMPLETED);
    }

    /**
     * Determines whether the deployment failed.
     *
     * @return bool
     */
    public function isFailed()
    {
        return ($this->status === self::FAILED);
    }

    /**
     * Determines if the deployment is the latest deployment.
     *
     * @return bool
     */
    public function isCurrent()
    {
        if (!isset(self::$currentDeployment[$this->project_id])) {
            self::$currentDeployment[$this->project_id] = self::where('project_id', $this->project_id)
                                                              ->where('status', self::COMPLETED)
                                                              ->orderBy('id', 'desc')
                                                              ->first();
        }

        return (self::$currentDeployment[$this->project_id]->id === $this->id);
    }

    /**
     * Determines how long the deploy took.
     *
     * @return false|int False if the deploy is still running, otherwise the runtime in seconds
     */
    public function runtime()
    {
        if (!$this->finished_at) {
            return;
        }

        return $this->started_at->diffInSeconds($this->finished_at);
    }

    /**
     * Gets the HTTP URL to the commit.
     *
     * @return string|false
     */
    public function getCommitUrlAttribute()
    {
        if ($this->commit !== self::LOADING) {
            $info = $this->project->accessDetails();
            if (isset($info['domain']) && isset($info['reference'])) {
                return 'http://' . $info['domain'] . '/' . $info['reference'] . '/commit/' . $this->commit;
            }
        }

        return false;
    }

    /**
     * Gets the short commit hash.
     *
     * @return string
     */
    public function getShortCommitAttribute()
    {
        if ($this->commit !== self::LOADING) {
            return substr($this->commit, 0, 7);
        }

        return $this->commit;
    }

    /**
     * Gets the HTTP URL to the branch.
     *
     * @return string|false
     * @see \App\Project::accessDetails()
     */
    public function getBranchURLAttribute()
    {
        $info = $this->project->accessDetails();

        if (isset($info['domain']) && isset($info['reference'])) {
            return 'http://' . $info['domain'] . '/' . $info['reference'] . '/tree/' . $this->branch;
        }

        return false;
    }

    /**
     * Generates a slack payload for the deployment.
     *
     * @return array
     */
    public function notificationPayload()
    {
        $colour  = 'good';
        $message = Lang::get('notifications.success_message');

        if ($this->status === self::FAILED) {
            $colour  = 'danger';
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
                            'value' => sprintf('<%s|%s>', url('projects', $this->project_id), $this->project->name),
                            'short' => true,
                        ], [
                            'title' => Lang::get('notifications.commit'),
                            'value' => $this->commit_url ? sprintf(
                                '<%s|%s>',
                                $this->commit_url,
                                $this->short_commit
                            ) : $this->short_commit,
                            'short' => true,
                        ], [
                            'title' => Lang::get('notifications.committer'),
                            'value' => $this->committer,
                            'short' => true,
                        ], [
                            'title' => Lang::get('notifications.branch'),
                            'value' => $this->project->branch,
                            'short' => true,
                        ],
                    ],
                ],
            ],
        ];

        return $payload;
    }

    /**
     * Gets the view presenter.
     *
     * @return DeploymentPresenter
     */
    public function getPresenter()
    {
        return new DeploymentPresenter($this);
    }

    /**
     * Define a accessor for the project name.
     *
     * @return string
     */
    public function getProjectNameAttribute()
    {
        return $this->project->name;
    }

    /**
     * Define a accessor for the deployer name.
     *
     * @return string
     */
    public function getDeployerNameAttribute()
    {
        if (!empty($this->user_id)) {
            return $this->user->name;
        }

        return $this->getPresenter()->committer_name;
    }

    /**
     * Checks whether the repository failed to load.
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getRepoFailureAttribute()
    {
        return ($this->commit === self::LOADING && $this->status === self::FAILED);
    }
}
