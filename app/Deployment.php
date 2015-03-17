<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deployment extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'deployments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['committer', 'commit', 'project_id', 'status'];


    public function getDates()
    {
        return ['created_at', 'run', 'updated_at'];
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
