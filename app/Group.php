<?php

namespace App;

use App\Traits\BroadcastChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Group model.
 */
class Group extends Model
{
    use SoftDeletes, BroadcastChanges;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'deleted_at', 'updated_at'];

    /**
     * Additional attributes to include in the JSON representation.
     *
     * @var array
     */
    protected $appends = ['project_count'];

    /**
     * Has many relationship.
     *
     * @return Project
     */
    public function projects()
    {
        return $this->hasMany('App\Project')
                    ->notTemplates()
                    ->orderBy('name');
    }

    /**
     * Define a accessor for the count of projects.
     *
     * @return int
     */
    public function getProjectCountAttribute()
    {
        return $this->projects()
                    ->count();
    }
}
