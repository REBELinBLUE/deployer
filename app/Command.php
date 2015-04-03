<?php namespace App;

use App\Server;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Command extends Model
{
    use SoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['project', 'created_at', 'deleted_at', 'updated_at'];

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function servers()
    {
        return $this->belongsToMany('App\Server');
    }

    /**
     * FIXME: See if laravel has a built in way of handling this
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
