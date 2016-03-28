<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use REBELinBLUE\Deployer\Presenters\ProjectPresenter;
use REBELinBLUE\Deployer\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;
use Robbo\Presenter\PresentableInterface;
use Version\Compare as VersionCompare;

/**
 * Project model.
 */
class Project extends ProjectRelation implements PresentableInterface
{
    use SoftDeletes, BroadcastChanges;

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
                         'notifications', 'deployments', 'shareFiles', 'projectFiles',
                         'is_template', 'last_mirrored', ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'repository', 'branch', 'group_id', 'include_dev',
                           'builds_to_keep', 'url', 'build_url', 'is_template', 'allow_other_branch',
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
    protected $appends = ['group_name', 'webhook_url', 'repository_path', 'repository_url', 'branch_url'];

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
        'is_template'        => 'boolean',
        'allow_other_branch' => 'boolean',
        'include_dev'        => 'boolean',
    ];

    /**
     * The heart beats status count.
     * @var array
     */
    protected $heartbeatStatus = [];

    /**
     * The check url's status count.
     * @var array
     */
    protected $checkurlStatus = [];

    /**
     * Override the boot method to bind model event listeners.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        // When first creating the model generate an SSH Key pair and a webhook hash
        static::creating(function (Project $model) {
            if (!array_key_exists('private_key', $model->attributes)) {
                $model->generateSSHKey();
            }

            if (!array_key_exists('public_key', $model->attributes)) {
                $model->regeneratePublicKey();
            }

            if (!array_key_exists('hash', $model->attributes)) {
                $model->generateHash();
            }
        });

        // When updating the model regenerate the public key if the private key has been changed
        static::updating(function (Project $model) {
            if (!array_key_exists('public_key', $model->attributes) || $model->public_key === '') {
                $model->regeneratePublicKey();
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
     * Determines whether the servers assigned to this project have all been tested and are deployable.
     *
     * @return bool
     */
    public function canDeploy()
    {
        // Check if there are any servers
        if (!count($this->servers)) {
            return false;
        }

        // Go through each server as test if it is online
        foreach ($this->servers as $server) {
            if (!$server->isOnline()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Generates a hash for use in the webhook URL.
     *
     * @return void
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
     * @see \REBELinBLUE\Deployer\Project::accessDetails()
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
     * @see \REBELinBLUE\Deployer\Project::accessDetails()
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
     * @return string|false
     * @see \REBELinBLUE\Deployer\Project::accessDetails()
     */
    public function getBranchUrlAttribute()
    {
        $info = $this->accessDetails();

        if (isset($info['domain']) && isset($info['reference'])) {
            return 'http://' . $info['domain'] . '/' . $info['reference'] . '/tree/' . $this->branch;
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
                if ($link->last_status) {
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
     * @return int
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
        return route('webhook', $this->hash);
    }

    /**
     * Query scope to not show templates.
     *
     * @param  object $query
     * @return object
     */
    public function scopeNotTemplates($query)
    {
        return $query->where('is_template', '=', false);
    }

    /**
     * Generates an SSH key and sets the private/public key properties.
     *
     * @return void
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
     *
     * @return void
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
     * @return Collection
     */
    public function tags()
    {
        $tags = $this->refs()
                     ->where('is_tag', true)
                     ->lists('name')
                     ->toArray();

        $compare = new VersionCompare;
        usort($tags, [$compare, 'compare']);

        return collect($tags);
    }

    /**
     * Gets the list of all branches for the project which are not the default.
     *
     * @return Collection
     */
    public function branches()
    {
        return $this->refs()
                    ->where('is_tag', false)
                    ->where('name', '<>', $this->branch)
                    ->orderBy('name')
                    ->lists('name');
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
}
