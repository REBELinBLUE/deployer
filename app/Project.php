<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes; // FIXME: Add protected private_key, public_key, last_run to protected

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'repository', 'branch', 'url', 'build_url'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['private_key'];

    public function getDates()
    {
        return ['created_at', 'last_run', 'updated_at'];
    }

    public function servers()
    {
        return $this->hasMany('App\Server');
    }
    
    public function deployments()
    {
        return $this->hasMany('App\Deployment');
    }
}
