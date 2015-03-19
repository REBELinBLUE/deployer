<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ServerLog extends Model
{
    public function server()
    {
        return $this->belongsTo('App\Server');
    }

    public function getDates()
    {
        return ['created_at', 'started_at', 'finished_at', 'updated_at'];
    }

    public function runtime()
    {
        if (!$this->finished_at) {
            return false;
        }

        return $this->started_at->diffInSeconds($this->finished_at);
    }
}
