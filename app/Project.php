<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
