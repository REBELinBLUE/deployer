<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Str;

use Symfony\Component\Process\Process;

class Project extends Model
{
    use SoftDeletes; // FIXME: Add protected private_key, public_key, last_run to protected

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['private_key', 'commands', 'created_at', 'deleted_at', 'hash', 'last_run', 'public_key', 'servers', 'updated_at', 'status'];

    public function getDates()
    {
        return ['created_at', 'last_run', 'updated_at'];
    }

    public function isDeploying()
    {
        return ($this->status == 'Deploying' || $this->status == 'Pending');
    }

    public function servers()
    {
        return $this->hasMany('App\Server');
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
