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
     * Belongs to assocation
     *
     * @return Server
     */
    public function server()
    {
        return $this->belongsTo('App\Server');
    }

    /**
     * Overwrite Laravel's getDate() function to add additional dates
     *
     * @return array
     */
    public function getDates()
    {
        return ['created_at', 'started_at', 'finished_at', 'updated_at'];
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
