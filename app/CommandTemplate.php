<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommandTemplate extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'user', 'script', 'template_id', 'step', 'order', 'optional'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'step'     => 'integer',
        'optional' => 'boolean'
    ];

    /**
     * Belongs to relationship
     *
     * @return Template
     */
    public function template()
    {
        return $this->belongsTo('App\Template');
    }

    /**
     * Has many relationship
     *
     * @return Command
     */
    public function commands()
    {
        return $this->hasMany('App\CommandTemplate');
    }
}
