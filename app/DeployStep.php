<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * The deployment step model
 */
class DeployStep extends Model
{
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'stage'    => 'integer',
        'optional' => 'boolean'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['stage', 'deployment_id', 'command_id'];

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
