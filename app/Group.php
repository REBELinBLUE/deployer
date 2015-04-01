<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['name'];

    public function projects()
    {
        return $this->hasMany('App\Project');
    }

}
