<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Server model
 */
class Server extends Model
{
    use SoftDeletes;

    const SUCCESSFUL = 0;
    const UNTESTED   = 1;
    const FAILED     = 2;
    const TESTING    = 3;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['project_id', 'created_at', 'updated_at', 'deleted_at', 'pivot'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'user', 'ip_address', 'project_id', 'path', 'status'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer'
    ];

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
