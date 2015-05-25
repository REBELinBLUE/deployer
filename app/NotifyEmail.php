<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Email list for a deployment notification.
 */
class NotifyEmail extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'project_id'];

    /**
     * Belongs to relationship
     *
     * @return Project
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }
}
