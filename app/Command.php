<?php namespace App;

use App\Server;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * The commanbd model
 */
class Command extends Model
{
    use SoftDeletes;

    const BEFORE_CLONE    = 1;
    const DO_CLONE        = 2;
    const AFTER_CLONE     = 3;
    const BEFORE_INSTALL  = 4;
    const DO_INSTALL      = 5;
    const AFTER_INSTALL   = 6;
    const BEFORE_ACTIVATE = 7;
    const DO_ACTIVATE     = 8;
    const AFTER_ACTIVATE  = 9;
    const BEFORE_PURGE    = 10;
    const DO_PURGE        = 11;
    const AFTER_PURGE     = 12;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['project', 'created_at', 'deleted_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'user', 'script', 'project_id', 'step', 'order'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'step' => 'integer'
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

    /**
     * Belongs to many relationship
     *
     * @return Server
     */
    public function servers()
    {
        return $this->belongsToMany('App\Server');
    }

    /**
     * Checks if the server is assigned to this command
     *
     * @param Server $server The server to check
     * @return boolean
     * @todo See if laravel has a built in way of handling this
     */
    public function hasServer(Server $server)
    {
        foreach ($this->servers as $test) {
            if ($test->id === $server->id) {
                return true;
            }
        }

        return false;
    }
}
