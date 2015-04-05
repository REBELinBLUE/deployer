<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * The deployment step model
 */
class DeployStep extends Model
{
    /**
     * Has many relationship
     *
     * @return ServerLog
     */
    public function servers()
    {
        return $this->hasMany('App\ServerLog');
    }

    /**
     * Belong to relationship
     *
     * @return Command
     */
    public function command()
    {
        return $this->belongsTo('App\Command');
    }
}
