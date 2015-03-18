<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Command extends Model
{
    use SoftDeletes; // FIXME: Add protected private_key, public_key, last_run

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'commands';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'script', 'project_id', 'order', 'step'];

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

}
