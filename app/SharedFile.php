<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\ModelChanged;
use App\Events\ModelCreated;
use App\Events\ModelTrashed;

/**
 * Shared files or directories for a project.
 */
class SharedFile extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'file', 'project_id'];

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

        static::updated(function (SharedFile $model) {
            event(new ModelChanged($model, 'share'));
        });

        static::created(function (SharedFile $model) {
            event(new ModelCreated($model, 'share'));
        });

        static::deleted(function (SharedFile $model) {
            event(new ModelTrashed($model, 'share'));
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
