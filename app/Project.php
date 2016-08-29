<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use REBELinBLUE\Deployer\Presenters\ProjectPresenter;
use REBELinBLUE\Deployer\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;
use REBELinBLUE\Deployer\Traits\ProjectRelations;
use Robbo\Presenter\PresentableInterface;
use UnexpectedValueException;
use Version\Compare as VersionCompare;

/**
 * Project model.
 *
 * @property integer $id
 * @property string $name
 * @property string $repository
 * @property string $hash
 * @property string $branch
 * @property string $private_key
 * @property string $public_key
 * @property integer $group_id
 * @property integer $builds_to_keep
 * @property string $url
 * @property string $build_url
 * @property string $status
 * @property \Carbon\Carbon $last_run
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property \Carbon\Carbon $last_mirrored
 * @property boolean $allow_other_branch
 * @property boolean $include_dev
 * @property-read mixed $repository_path
 * @property-read mixed $repository_url
 * @property-read mixed $branch_url
 * @property-read mixed $group_name
 * @property-read mixed $webhook_url
 * @property-read Group $group
 * @property-read Server[] $servers
 * @property-read Heartbeat[] $heartbeats
 * @property-read Notification[] $notifications
 * @property-read Deployment[] $deployments
 * @property-read Command[] $commands
 * @property-read Variable[] $variables
 * @property-read SharedFile[] $sharedFiles
 * @property-read ConfigFile[] $configFiles
 * @property-read NotifyEmail[] $notifyEmails
 * @property-read CheckUrl[] $checkUrls
 * @property-read Ref[] $refs
 */
class Project extends Model implements PresentableInterface
{
    use SoftDeletes, BroadcastChanges, ProjectRelations;

    const FINISHED     = 0;
    const PENDING      = 1;
    const DEPLOYING    = 2;
    const FAILED       = 3;
    const NOT_DEPLOYED = 4;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['private_key', 'created_at', 'deleted_at', 'updated_at', 'hash',
                         'updated_at', 'servers', 'commands', 'hash', 'notifyEmails',
                         'group', 'servers', 'commands', 'heartbeats', 'checkUrls',
                         'notifications', 'deployments', 'shareFiles', 'configFiles', 'last_mirrored', ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'repository', 'branch', 'group_id', 'include_dev',
                           'builds_to_keep', 'url', 'build_url', 'allow_other_branch',
                           'private_key', ];

    /**
     * The fields which should be treated as Carbon instances.
     *
     * @var array
     */
    protected $dates = ['last_run', 'last_mirrored'];

    /**
     * Additional attributes to include in the JSON representation.
     *
     * @var array
     */
    protected $appends = ['application_status', 'heartbeat_status', 'webhook_url',
                          'repository_path', 'repository_url', 'branch_url', ];

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
    ];

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
     * Override the boot method to bind model event listeners.
     */
    public static function boot()
    {
        parent::boot();

        // When  creating the model generate an SSH Key pair and a webhook hash
        static::saving(function (Project $model) {
            if (!array_key_exists('private_key', $model->attributes) || $model->private_key === '') {
                $model->generateSSHKey();
            }

            if (!array_key_exists('public_key', $model->attributes) || $model->public_key === '') {
                $model->regeneratePublicKey();
            }

            if (!array_key_exists('hash', $model->attributes)) {
                $model->generateHash();
            }
        });
    }

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
        $this->attributes['hash'] = Str::random(60);
    }

    /**
     * Parses the repository URL to get the user, domain, port and path parts.
     *
     * @return array
     */
    public function accessDetails()
    {
        $info = [];

        if (preg_match('#^(.+)@(.+):([0-9]*)\/?(.+)\.git$#', $this->repository, $matches)) {
            $info['user']      = $matches[1];
            $info['domain']    = $matches[2];
            $info['port']      = $matches[3];
            $info['reference'] = $matches[4];
        } elseif (preg_match('#^https?#', $this->repository)) {
            $data = parse_url($this->repository);

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
            return 'http://' . $info['domain'] . '/' . $info['reference'];
        }

        return false;
    }

    /**
     * Gets the view presenter.
     *
     * @return ProjectPresenter
     */
    public function getPresenter()
    {
        return new ProjectPresenter($this);
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
            if (preg_match('/bitbucket/', $info['domain'])) {
                $path = 'commits/branch';
            }

            $branch = (is_null($alternative) ? $this->branch : $alternative);

            return 'http://' . $info['domain'] . '/' . $info['reference'] . '/' . $path . '/' . $branch;
        }

        return false;
    }

    /**
     * Count the missed heartbeat.
     *
     * @return array
     */
    public function getHeartbeatStatusAttribute()
    {
        if (empty($this->heartbeatStatus)) {
            $length = count($this->heartbeats);
            $missed = 0;

            foreach ($this->heartbeats as $beat) {
                if (!$beat->isHealthy()) {
                    $missed++;
                }
            }

            $this->heartbeatStatus = ['missed' => $missed, 'total' => $length];
        }

        return $this->heartbeatStatus;
    }

    /**
     * Count the application url check status.
     *
     * @return array
     */
    public function getApplicationStatusAttribute()
    {
        if (empty($this->checkurlStatus)) {
            $length = count($this->checkUrls);
            $missed = 0;

            foreach ($this->checkUrls as $link) {
                if ($link->last_status) {
                    $missed++;
                }
            }

            $this->checkurlStatus = ['missed' => $missed, 'total' => $length];
        }

        return $this->checkurlStatus;
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
     * Generates an SSH key and sets the private/public key properties.
     */
    protected function generateSSHKey()
    {
        $key = tempnam(storage_path('app/'), 'sshkey');
        unlink($key);

        $process = new Process('tools.GenerateSSHKey', [
            'key_file' => $key,
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $this->attributes['private_key'] = file_get_contents($key);
        $this->attributes['public_key']  = file_get_contents($key . '.pub');

        unlink($key);
        unlink($key . '.pub');
    }

    /**
     * Generates an SSH key and sets the private/public key properties.
     */
    protected function regeneratePublicKey()
    {
        $key = tempnam(storage_path('app/'), 'sshkey');
        file_put_contents($key, $this->private_key);

        $process = new Process('tools.RegeneratePublicSSHKey', [
            'key_file' => $key,
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $this->attributes['public_key']  = file_get_contents($key . '.pub');

        unlink($key);
        unlink($key . '.pub');
    }

    /**
     * Gets the list of all tags for the project.
     *
     * @return \Illuminate\Support\Collection
     */
    public function tags()
    {
        $tags = $this->refs()
                     ->where('is_tag', true)
                     ->pluck('name')
                     ->toArray();

        $compare = new VersionCompare;

        // Sort the tags, if compare throws an exception it isn't a value version string so just do a strnatcmp
        @usort($tags, function ($first, $second) use ($compare) {
            try {
                return $compare->compare($first, $second);
            } catch (UnexpectedValueException $error) {
                return strnatcmp($first, $second);
            }
        });

        return collect($tags);
    }

    /**
     * Gets the list of all branches for the project which are not the default.
     *
     * @return array
     */
    public function branches()
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
     * @return Group
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Has many relationship.
     *
     * @return Server
     */
    public function servers()
    {
        return $this->hasMany(Server::class)
                    ->orderBy('order', 'ASC');
    }

    /**
     * Has many relationship.
     *
     * @return Heartbeat
     */
    public function heartbeats()
    {
        return $this->hasMany(Heartbeat::class)
                    ->orderBy('name');
    }

    /**
     * Has many relationship.
     *
     * @return Notification
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)
                    ->orderBy('name');
    }

    /**
     * Has many relationship.
     *
     * @return Deployment
     */
    public function deployments()
    {
        return $this->hasMany(Deployment::class)
                    ->orderBy('started_at', 'DESC');
    }

    /**
     * Has many relationship.
     *
     * @return SharedFile
     */
    public function notifyEmails()
    {
        return $this->hasMany(NotifyEmail::class);
    }

    /**
     * Has many urls to check.
     *
     * @return CheckUrl
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
     * @see Project::tags()
     * @see Project::branches()
     */
    public function refs()
    {
        return $this->hasMany(Ref::class);
    }
}
