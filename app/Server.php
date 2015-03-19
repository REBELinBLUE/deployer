<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Server extends Model
{
    use SoftDeletes; // FIXME: Add protected private_key, public_key, last_run
    
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

    public function isTesting()
    {
        return ($this->status === 'Testing');
    }
}
