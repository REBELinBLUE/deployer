<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Server extends Model
{
    use SoftDeletes;

    const SUCCESSFUL = 'Successful';
    const TESTING    = 'Testing';
    const FAILED     = 'Failed';
    const UNTESTED   = 'Untested';
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['project_id', 'created_at', 'updated_at', 'deleted_at', 'pivot'];

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function isTesting()
    {
        return ($this->status === 'Testing');
    }
}
