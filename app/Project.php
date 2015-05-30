<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Robbo\Presenter\PresentableInterface;
use App\Presenters\ProjectPresenter;

/**
 * Project model
 */
class Project extends Model implements PresentableInterface
{
    use SoftDeletes;

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
    protected $hidden = ['private_key', 'public_key', 'created_at', 'deleted_at', 'heartbeats',
                         'updated_at', 'servers', 'commands', 'hash', 'status'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'repository', 'branch', 'group_id', 'builds_to_keep', 'url', 'build_url'];

    /**
     * The fields which should be tried as Carbon instances
     *
     * @var array
     */
    protected $dates = ['last_run'];

    /**
     * Additional attributes to include in the JSON representation
     *
     * @var array
     */
    protected $appends = ['group_name'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'status'         => 'integer',
        'builds_to_keep' => 'integer'
    ];

    /**
     * Override the boot method to bind model event listeners
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        // When first creating the model generate an SSH Key pair and a webhook hash
        static::creating(function ($model) {
            if (!array_key_exists('private_key', $model->attributes)) {
                $model->generateSSHKey();
            }

            if (!array_key_exists('hash', $model->attributes)) {
                $model->generateHash();
            }
        });
    }

    /**
     * Determines whether the project is currently being deployed
     *
     * @return boolean
     */
    public function isDeploying()
    {
        return ($this->status === self::DEPLOYING || $this->status === self::PENDING);
    }

    /**
     * Belongs to relationship
     *
     * @return Group
     */
    public function group()
    {
        return $this->belongsTo('App\Group');
    }

    /**
     * Has many relationship
     *
     * @return Server
     */
    public function servers()
    {
        return $this->hasMany('App\Server')->orderBy('name');
    }

    /**
     * Has many relationship
     *
     * @return Heartbeat
     */
    public function heartbeats()
    {
        return $this->hasMany('App\Heartbeat')->orderBy('name');
    }

    /**
     * Has many relationship
     *
     * @return Notification
     */
    public function notifications()
    {
        return $this->hasMany('App\Notification')->orderBy('name');
    }

    /**
     * Has many relationship
     *
     * @return Deployment
     */
    public function deployments()
    {
        return $this->hasMany('App\Deployment')->orderBy('started_at', 'DESC');
    }

    /**
     * Has many relationship
     *
     * @return Command
     */
    public function commands()
    {
        return $this->hasMany('App\Command');
    }

    /**
     * Has many relationship
     * @return SharedFile
     */
    public function shareFiles()
    {
        return $this->hasMany('App\SharedFile');
    }

    /**
     * Has many relationship to project file
     * @return ProjectFile
     */
    public function projectFiles()
    {
        return $this->hasMany('App\ProjectFile');
    }

    /**
     * Has many relationship
     * @return SharedFile
     */
    public function notifyEmails()
    {
        return $this->hasMany('App\NotifyEmail');
    }

    /**
     * Generates a hash for use in the webhook URL
     *
     * @return void
     */
    public function generateHash()
    {
        $this->attributes['hash'] = Str::random(60);
    }

    /**
     * Parses the repository URL to get the user, domain, port and path parts
     *
     * @return array
     */
    public function accessDetails()
    {
        $info = [];

        if (preg_match('#^(.+)@(.+):([0-9]*)\/?(.+)\.git#', $this->repository, $matches)) {
            $info['user'] = $matches[1];
            $info['domain'] = $matches[2];
            $info['port'] = $matches[3];
            $info['reference'] = $matches[4];
        }

        return $info;
    }

    /**
     * Gets the repository path
     *
     * @return string|false
     * @see \App\Project::accessDetails()
     * TODO: Should this be an attribute?
     */
    public function repositoryPath()
    {
        $info = $this->accessDetails();

        if (isset($info['reference'])) {
            return $info['reference'];
        }

        return false;
    }

    /**
     * Gets the HTTP URL to the repository
     *
     * @return string|false
     * @see \App\Project::accessDetails()
     * TODO: Should this be an attribute?
     */
    public function repositoryURL()
    {
        $info = $this->accessDetails();

        if (isset($info['domain']) && isset($info['reference'])) {
            return 'http://' . $info['domain'] . '/' . $info['reference'];
        }

        return false;
    }

    /**
     * Gets the view presenter
     *
     * @return ProjectPresenter
     */
    public function getPresenter()
    {
        return new ProjectPresenter($this);
    }

    /**
     * Gets the HTTP URL to the branch
     *
     * @return string|false
     * @see \App\Project::accessDetails()
     * TODO: Should this be an attribute?
     */
    public function branchURL()
    {
        $info = $this->accessDetails();

        if (isset($info['domain']) && isset($info['reference'])) {
            return 'http://' . $info['domain'] . '/' . $info['reference'] . '/tree/' . $this->branch;
        }

        return false;
    }

    /**
     * Define a mutator for the group name
     *
     * @return int
     */
    public function getGroupNameAttribute()
    {
        return $this->group->name;
    }

    /**
     * Generates an SSH key and sets the private/public key properties
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function generateSSHKey()
    {
        $key = tempnam(storage_path() . '/app/', 'sshkey');
        unlink($key);

        $process = new Process(sprintf(
            'ssh-keygen -t rsa -b 2048 -f %s -N "" -C "deploy@deployer"',
            $key
        ));

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $this->attributes['private_key'] = file_get_contents($key);
        $this->attributes['public_key']  = file_get_contents($key . '.pub');

        unlink($key);
        unlink($key . '.pub');
    }
}
