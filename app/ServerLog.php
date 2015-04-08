<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Server log model
 */
class ServerLog extends Model
{
    const COMPLETED = 0;
    const PENDING   = 1;
    const RUNNING   = 2;
    const FAILED    = 3;
    const CANCELLED = 4;

    /**
     * The fields which should be tried as Carbon instances
     * 
     * @var array
     */
    protected $dates = ['started_at', 'finished_at'];

    /**
     * Belongs to assocation
     *
     * @return Server
     */
    public function server()
    {
        return $this->belongsTo('App\Server');
    }

    /**
     * Calculates how long the commands were running on the server for
     *
     * @return false|int Returns false if the command has not yet finished or the runtime in seconds
     */
    public function runtime()
    {
        if (!$this->finished_at) {
            return false;
        }

        return $this->started_at->diffInSeconds($this->finished_at);
    }
}
