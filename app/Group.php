<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Group model
 */
class Group extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Has many relationshop
     *
     * @return Project
     */
    public function projects()
    {
        return $this->hasMany('App\Project');
    }
}
