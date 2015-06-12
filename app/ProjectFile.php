<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\ModelChanged;
use App\Events\ModelCreated;
use App\Events\ModelTrashed;

/**
 * Static file for project.
 */
class ProjectFile extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'path', 'content', 'project_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Override the boot method to bind model event listeners.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::updated(function (ProjectFile $model) {
            event(new ModelChanged($model, 'file'));
        });

        static::created(function (ProjectFile $model) {
            event(new ModelCreated($model, 'file'));
        });

        static::deleted(function (ProjectFile $model) {
            event(new ModelTrashed($model, 'file'));
        });
    }

    /**
     * Belongs to relationship.
     *
     * @return Project
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }
}
