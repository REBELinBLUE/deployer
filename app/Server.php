<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Server extends Model
{
    use SoftDeletes; // FIXME: Add protected private_key, public_key, last_run

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'servers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'ip_address', 'user', 'path', 'project_id'];

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function isTesting()
    {
        return ($this->status === 'Testing');
    }
}
