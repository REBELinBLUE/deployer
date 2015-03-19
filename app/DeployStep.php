<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class DeployStep extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'deploy_steps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['deployment_id', 'stage', 'command_id', 'run_order'];

    public function servers()
    {
        return $this->hasMany('App\ServerLog');
    }

    public function command()
    {
        return $this->belongsTo('App\Command');
    }
}
