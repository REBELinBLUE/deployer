<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class Project extends Model
{
    use SoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['private_key', 'public_key', 'created_at', 'deleted_at',
                         'updated_at', 'last_run', 'servers', 'commands', 'hash', 'status'];

    public function getDates()
    {
        return ['created_at', 'last_run', 'updated_at'];
    }

    public function isDeploying()
    {
        return ($this->status == 'Deploying' || $this->status == 'Pending');
    }

    public function group()
    {
        return $this->belongsTo('App\Group');
    }

    public function servers()
    {
        return $this->hasMany('App\Server');
    }

    public function notifications()
    {
        return $this->hasMany('App\Notification');
    }

    public function deployments()
    {
        return $this->hasMany('App\Deployment');
    }

    public function commands()
    {
        return $this->hasMany('App\Command');
    }

    public function generateHash()
    {
        $this->hash = Str::random(60);
    }

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

    public function repositoryPath()
    {
        $info = $this->accessDetails();

        if (isset($info['reference'])) {
            return $info['reference'];
        }

        return false;
    }

    public function repositoryURL()
    {
        $info = $this->accessDetails();

        if (isset($info['domain']) && isset($info['reference'])) {
            return 'http://' . $info['domain'] . '/' . $info['reference'];
        }

        return false;
    }

    public function branchURL()
    {
        $info = $this->accessDetails();

        if (isset($info['domain']) && isset($info['reference'])) {
            return 'http://' . $info['domain'] . '/' . $info['reference'] . '/tree/' . $this->branch;
        }

        return false;
    }

    public function generateSSHKey()
    {
        $key = tempnam(storage_path() . '/app/', 'sshkey');
        unlink($key);

        $process = new Process(sprintf('ssh-keygen -t rsa -b 2048 -f %s -N "" -C "deploy@deployer"', $key));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $this->private_key = file_get_contents($key);
        $this->public_key = file_get_contents($key . '.pub');

        unlink($key);
        unlink($key . '.pub');
    }
}
