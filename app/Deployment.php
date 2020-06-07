<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use McCool\LaravelAutoPresenter\HasPresenter;
use REBELinBLUE\Deployer\Events\ModelChanged;
use REBELinBLUE\Deployer\View\Presenters\DeploymentPresenter;
use REBELinBLUE\Deployer\View\Presenters\RuntimeInterface;

/**
 * Deployment model.
 */
class Deployment extends Model implements HasPresenter, RuntimeInterface
{
    use SoftDeletes;

    public const COMPLETED             = 0;
    public const PENDING               = 1;
    public const DEPLOYING             = 2;
    public const FAILED                = 3;
    public const COMPLETED_WITH_ERRORS = 4;
    public const ABORTING              = 5;
    public const ABORTED               = 6;
    public const LOADING               = 'Loading';

    public static $currentDeployment = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['reason', 'branch', 'project_id', 'source', 'build_url',
                           'commit', 'committer_email', 'committer', ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'deleted_at', 'updated_at', 'user', 'commands'];

    /**
     * Additional attributes to include in the JSON representation.
     *
     * @var array
     */
    protected $appends = ['project_name', 'deployer_name', 'commit_url',
                          'short_commit', 'branch_url', 'repo_failure', ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'project_id' => 'integer',
        'user_id'    => 'integer',
        'status'     => 'integer',
        'is_webhook' => 'boolean',
    ];

    /**
     * The fields which should be treated as Carbon instances.
     *
     * @var array
     */
    protected $dates = ['started_at', 'finished_at'];

    /**
     * Override the boot method to bind model event listeners.
     */
    public static function boot()
    {
        parent::boot();

        // FIXME: Change to use the trait
        static::saved(function (self $model) {
            event(new ModelChanged($model, 'deployment'));
        });
    }

    /**
     * Belongs to relationship.
     *
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Belongs to relationship.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)
                    ->withTrashed();
    }

    /**
     * Define a command attribute to be able to access to commands relationship.
     *
     * @return Collection
     */
    public function getCommandsAttribute(): Collection
    {
        if (!$this->relationLoaded('commands')) {
            $this->loadCommands();
        }

        if ($this->relationLoaded('commands')) {
            return $this->getRelation('commands');
        }

        return collect([]);
    }

    /**
     * Has many relationship.
     *
     * @return HasMany
     */
    public function steps()
    {
        return $this->hasMany(DeployStep::class);
    }

    /**
     * Determines whether the deployment is running.
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        return ($this->status === self::DEPLOYING);
    }

    /**
     * Determines whether the deployment is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return ($this->status === self::PENDING);
    }

    /**
     * Determines whether the deployment is successful.
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return ($this->status === self::COMPLETED);
    }

    /**
     * Determines whether the deployment failed.
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return ($this->status === self::FAILED);
    }

    /**
     * Determines whether the deployment is waiting to be aborted.
     *
     * @return bool
     */
    public function isAborting(): bool
    {
        return ($this->status === self::ABORTING);
    }

    /**
     * Determines whether the deployment is aborted.
     *
     * @return bool
     */
    public function isAborted(): bool
    {
        return ($this->status === self::ABORTED);
    }

    /**
     * Determines if the deployment is the latest deployment.
     *
     * @return bool
     */
    public function isCurrent(): bool
    {
        if (!isset(self::$currentDeployment[$this->project_id])) {
            self::$currentDeployment[$this->project_id] = self::where('project_id', $this->project_id)
                                                              ->where('status', self::COMPLETED)
                                                              ->orderBy('id', 'desc')
                                                              ->first();
        }

        if (isset(self::$currentDeployment[$this->project_id]) &&
            self::$currentDeployment[$this->project_id]->id === $this->id
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determines how long the deploy took.
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
     * Gets the HTTP URL to the commit.
     *
     * @return string|false
     */
    public function getCommitUrlAttribute()
    {
        if ($this->commit !== self::LOADING) {
            $info = $this->project->accessDetails();
            if (isset($info['domain']) && isset($info['reference'])) {
                $path = 'commit';
                if (preg_match('/bitbucket/', $info['domain'])) {
                    $path = 'commits';
                }

                return 'http://' . $info['domain'] . '/' . $info['reference'] . '/' . $path . '/' . $this->commit;
            }
        }

        return false;
    }

    /**
     * Gets the short commit hash.
     *
     * @return string
     */
    public function getShortCommitAttribute(): string
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
     *
     * @see Project::accessDetails()
     */
    public function getBranchUrlAttribute()
    {
        return $this->project->getBranchUrlAttribute($this->branch);
    }

    /**
     * Gets the view presenter.
     *
     * @return string
     */
    public function getPresenterClass(): string
    {
        return DeploymentPresenter::class;
    }

    /**
     * Define a accessor for the project name.
     *
     * @return string
     */
    public function getProjectNameAttribute(): string
    {
        return $this->project->name;
    }

    /**
     * Define a accessor for the deployer name.
     *
     * @return string|null
     */
    public function getDeployerNameAttribute(): ?string
    {
        if (!empty($this->user_id)) {
            return $this->user->name;
        } elseif (!empty($this->source)) {
            return $this->source;
        }

        // FIXME: This is horrible
        $presenter = $this->getPresenterClass();

        /** @var DeploymentPresenter $presenter */
        $presenter = new $presenter(app('translator'));

        return $presenter->setWrappedObject($this)->committer_name;
    }

    /**
     * Checks whether the repository failed to load.
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getRepoFailureAttribute(): bool
    {
        return ($this->commit === self::LOADING && $this->status === self::FAILED);
    }

    /**
     * Mutator to get the release ID.
     *
     * @return string
     */
    public function getReleaseIdAttribute(): string
    {
        return $this->started_at->format('YmdHis');
    }

    /**
     * Query the DB and load the HasMany relationship for commands.
     *
     * @return self
     */
    private function loadCommands(): self
    {
        $collection = Command::join('deploy_steps', 'commands.id', '=', 'deploy_steps.command_id')
                             ->where('deploy_steps.deployment_id', $this->getKey())
                             ->distinct()
                             ->orderBy('step')
                             ->orderBy('order')
                             ->get(['commands.*', 'deployment_id']);

        $hasMany = new HasMany(Command::query(), $this, 'deployment_id', 'id');
        $hasMany->matchMany([$this], $collection, 'commands');

        return $this;
    }
}
