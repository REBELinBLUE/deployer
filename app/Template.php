<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * The deployment template model
 */
class Template extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Additional attributes to include in the JSON representation
     *
     * @var array
     */
    protected $appends = ['command_count'];

    /**
     * Has many relationship
     *
     * @return Command
     */
    public function commands()
    {
        return $this->hasMany('App\CommandTemplate');
    }

    /**
     * Define a mutator for the count of commands
     *
     * @return int
     */
    public function getCommandCountAttribute()
    {
        return $this->commands()->count();
    }
}
