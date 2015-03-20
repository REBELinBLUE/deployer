<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Server;

class Command extends Model
{
    use SoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['project'];

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function servers()
    {
        return $this->belongsToMany('App\Server');
    }

    public function hasServer(Server $server)
    {
        foreach ($this->servers as $test)
        {
            if ($test->id === $server->id) {
                return true;
            }
        }

        return false;
    }
}
