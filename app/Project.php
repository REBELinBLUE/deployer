<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;
use REBELinBLUE\Deployer\Traits\ProjectRelations;
use REBELinBLUE\Deployer\View\Presenters\ProjectPresenter;
use UnexpectedValueException;
use Version\Compare as VersionCompare;

/**
 * Project model.
 */
class Project extends Model implements HasPresenter
{
    use SoftDeletes, BroadcastChanges, ProjectRelations;

    const FINISHED     = 0;
    const PENDING      = 1;
    const DEPLOYING    = 2;
    const FAILED       = 3;
    const NOT_DEPLOYED = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'repository', 'branch', 'group_id', 'include_dev',
                           'builds_to_keep', 'url', 'build_url', 'allow_other_branch',
                           'private_key', ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['private_key', 'created_at', 'deleted_at', 'updated_at', 'hash',
                         'updated_at', 'servers', 'commands', 'hash',
                         'group', 'servers', 'commands', 'heartbeats', 'checkUrls',
                         'notifications', 'deployments', 'shareFiles', 'configFiles', 'is_mirroring', ];

    /**
     * Additional attributes to include in the JSON representation.
     *
     * @var array
     */
    protected $appends = ['group_name', 'webhook_url', 'repository_path', 'repository_url',
                          'branch_url', 'tags', 'branches', ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                 => 'integer',
        'group_id'           => 'integer',
        'status'             => 'integer',
        'builds_to_keep'     => 'integer',
        'allow_other_branch' => 'boolean',
        'include_dev'        => 'boolean',
        'is_mirroring'       => 'boolean',
    ];

    /**
     * The fields which should be treated as Carbon instances.
     *
     * @var array
     */
    protected $dates = ['last_run', 'last_mirrored'];

    /**
     * The heart beats status count.
     *
     * @var array
     */
    protected $heartbeatStatus = [];

    /**
     * The check url's status count.
     *
     * @var array
     */
    protected $checkurlStatus = [];

    /**
     * Determines whether the project is currently being deployed.
     *
     * @return bool
     */
    public function isDeploying()
    {
        return ($this->status === self::DEPLOYING || $this->status === self::PENDING);
    }

    /**
     * Generates a hash for use in the webhook URL.
     */
    public function generateHash()
    {
        $this->attributes['hash'] = token(60);
    }

    /**
     * Parses the repository URL to get the user, domain, port and path parts.
     *
     * @return array
     */
    public function accessDetails()
    {
        $info = [];

        if (preg_match('#^(.+)://(.+)@(.+):([0-9]*)\/?(.+)\.git$#', $this->repository, $matches)) {
            $info['scheme']    = strtolower($matches[1]);
            $info['user']      = $matches[2];
            $info['domain']    = $matches[3];
            $info['port']      = $matches[4];
            $info['reference'] = $matches[5];
        } elseif (preg_match('#^(.+)@(.+):([0-9]*)\/?(.+)\.git$#', $this->repository, $matches)) {
            $info['scheme']    = 'git';
            $info['user']      = $matches[1];
            $info['domain']    = $matches[2];
            $info['port']      = $matches[3];
            $info['reference'] = $matches[4];
        } elseif (preg_match('#^https?://#i', $this->repository)) {
            $data = parse_url($this->repository);

            if (!$data) {
                return $info;
            }

            $info['scheme']    = strtolower($data['scheme']);
            $info['user']      = isset($data['user']) ? $data['user'] : '';
            $info['domain']    = $data['host'];
            $info['port']      = isset($data['port']) ? $data['port'] : '';
            $info['reference'] = substr($data['path'], 1, -4);
        }

        return $info;
    }

    /**
     * Gets the repository path.
     *
     * @return string|false
     *
     * @see Project::accessDetails()
     */
    public function getRepositoryPathAttribute()
    {
        $info = $this->accessDetails();

        if (isset($info['reference'])) {
            return $info['reference'];
        }

        return false;
    }

    /**
     * Gets the HTTP URL to the repository.
     *
     * @return string|false
     *
     * @see Project::accessDetails()
     */
    public function getRepositoryUrlAttribute()
    {
        $info = $this->accessDetails();

        if (isset($info['domain']) && isset($info['reference'])) {
            if (!isset($info['scheme']) || !starts_with($info['scheme'], 'http')) {
                $info['scheme'] = 'http';
            }

            // Always serve github links over HTTPS
            if (ends_with($info['domain'], 'github.com')) {
                $info['scheme'] = 'https';
            }

            return $info['scheme'] . '://' . $info['domain'] . '/' . $info['reference'];
        }

        return false;
    }

    /**
     * Gets the view presenter.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return ProjectPresenter::class;
    }

    /**
     * Gets the HTTP URL to the branch.
     *
     * @param string $alternative
     *
     * @return string|false
     *
     * @see Project::accessDetails()
     */
    public function getBranchUrlAttribute($alternative = null)
    {
        $info = $this->accessDetails();

        if (isset($info['domain']) && isset($info['reference'])) {
            $path = 'tree';
            if (str_contains($info['domain'], 'bitbucket')) {
                $path = 'commits/branch';
            }

            if (!isset($info['scheme']) || !starts_with($info['scheme'], 'http')) {
                $info['scheme'] = 'http';
            }

            // Always serve github links over HTTPS
            if (ends_with($info['domain'], 'github.com')) {
                $info['scheme'] = 'https';
            }

            $branch = (is_null($alternative) ? $this->branch : $alternative);

            return $info['scheme'] . '://' . $info['domain'] . '/' . $info['reference'] . '/' . $path . '/' . $branch;
        }

        return false;
    }

    /**
     * Count the missed heartbeat.
     *
     * @return array
     */
    public function heartbeatsStatus()
    {
        if (empty($this->heartbeatStatus)) {
            $length = count($this->heartbeats);
            $missed = 0;

            foreach ($this->heartbeats as $beat) {
                if (!$beat->isHealthy()) {
                    $missed++;
                }
            }

            $this->heartbeatStatus = ['missed' => $missed, 'length' => $length];
        }

        return $this->heartbeatStatus;
    }

    /**
     * Count the application url check status.
     *
     * @return array
     */
    public function applicationCheckUrlStatus()
    {
        if (empty($this->checkurlStatus)) {
            $length = count($this->checkUrls);
            $missed = 0;

            foreach ($this->checkUrls as $link) {
                if (!$link->isHealthy()) {
                    $missed++;
                }
            }

            $this->checkurlStatus = ['missed' => $missed, 'length' => $length];
        }

        return $this->checkurlStatus;
    }

    /**
     * Define a accessor for the group name.
     *
     * @return string
     */
    public function getGroupNameAttribute()
    {
        return $this->group->name;
    }

    /**
     * Define an accessor for the webhook URL.
     *
     * @return string
     */
    public function getWebhookUrlAttribute()
    {
        return route('webhook.deploy', $this->hash);
    }

    /**
     * Gets the list of all tags for the project.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTagsAttribute()
    {
        $tags = $this->refs()
                     ->where('is_tag', true)
                     ->pluck('name')
                     ->toArray();

        $compare = new VersionCompare();

        // Sort the tags, if compare throws an exception it isn't a value version string so just do a strnatcmp
        // See #258 - Can remove the @ when dropping PHP 5 support
        @usort($tags, function ($first, $second) use ($compare) {
            try {
                return $compare->compare($first, $second);
            } catch (UnexpectedValueException $error) {
                return strnatcmp($second, $first); // Move unknown versions to be bottle, swap round to move to top
            }
        });

        return collect($tags)->reverse()->values();
    }

    /**
     * Gets the list of all branches for the project which are not the default.
     *
     * @return array
     */
    public function getBranchesAttribute()
    {
        return $this->refs()
                    ->where('is_tag', false)
                    ->where('name', '<>', $this->branch)
                    ->orderBy('name')
                    ->pluck('name');
    }

    /**
     * Generate a friendly path for the mirror of the repository.
     * Use the repository rather than the project ID, so if a single
     * repo is used in multiple projects it is not duplicated.
     *
     * @return string
     */
    public function mirrorPath()
    {
        return storage_path('app/mirrors/' . preg_replace('/[^_\-.\-a-zA-Z0-9\s]/u', '_', $this->repository));
    }

    /**
     * Belongs to relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Has many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function servers()
    {
        return $this->hasMany(Server::class)
                    ->orderBy('order', 'ASC');
    }

    /**
     * Has many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function heartbeats()
    {
        return $this->hasMany(Heartbeat::class)
                    ->orderBy('name');
    }

    /**
     * Has many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deployments()
    {
        return $this->hasMany(Deployment::class)
                    ->orderBy('started_at', 'DESC');
    }

    /**
     * Has many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function channels()
    {
        return $this->hasMany(Channel::class)
                    ->orderBy('name');
    }

    /**
     * Has many urls to check.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checkUrls()
    {
        return $this->hasMany(CheckUrl::class);
    }

    /**
     * Has many relationship for git references.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @see Project::getTagsAttribute()
     * @see Project::getBranchesAttribute()
     */
    public function refs()
    {
        return $this->hasMany(Ref::class);
    }
}
