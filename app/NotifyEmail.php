<?php

namespace App;

use App\Events\ModelChanged;
use App\Events\ModelCreated;
use App\Events\ModelTrashed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Email list for a deployment notification.
 */
class NotifyEmail extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'project_id'];

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

        static::updated(function (NotifyEmail $model) {
            event(new ModelChanged($model, 'email'));
        });

        static::created(function (NotifyEmail $model) {
            event(new ModelCreated($model, 'email'));
        });

        static::deleted(function (NotifyEmail $model) {
            event(new ModelTrashed($model, 'email'));
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
