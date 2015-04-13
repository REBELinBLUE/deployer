<?php namespace App;

use App\Http\Requests\Request;
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
     * Additional attributes to include in the JSON representation
     *
     * @var array
     */
    protected $appends = ['project_count'];

    /**
     * Has many relationship
     *
     * @return Project
     */
    public function projects()
    {
        return $this->hasMany('App\Project');
    }

    /**
     * Define a mutator for the count of projects
     *
     * @return int
     */
    public function getProjectCountAttribute()
    {
        return $this->projects()->count();
    }
}

