<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\ModelChanged;
use App\Events\ModelCreated;
use App\Events\ModelTrashed;

/**
 * Group model.
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
     * Override the boot method to bind model event listeners.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function (Group $model) {
            event(new ModelCreated($model, 'group'));
        });

        static::updated(function (Group $model) {
            event(new ModelChanged($model, 'group'));
        });

        static::deleted(function (Group $model) {
            event(new ModelTrashed($model, 'group'));
        });
    }

    /**
     * Has many relationship.
     *
     * @return Project
     */
    public function projects()
    {
        return $this->hasMany('App\Project')
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
