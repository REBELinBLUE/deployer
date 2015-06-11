<?php

namespace App;

use Lang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\ModelChanged;
use App\Events\ModelCreated;
use App\Events\ModelTrashed;

/**
 * The application's  url store for health check.
 */
class CheckUrl extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'url', 'project_id', 'period', 'is_report'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'pivot'];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_report' => 'boolean'
    ];

    /**
     * Override the boot method to bind model event listeners.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function (CheckUrl $model) {
            event(new ModelCreated($model, 'link'));
        });

        static::updated(function (CheckUrl $model) {
            event(new ModelChanged($model, 'link'));
        });

        static::deleted(function (CheckUrl $model) {
            event(new ModelTrashed($model, 'link'));
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

    /**
     * Generates a slack payload for the link failure.
     *
     * @return array
     */
    public function notificationPayload()
    {
        $message = Lang::get('checkurls.message', ['link' => $this->title]);

        $payload = [
            'attachments' => [
                [
                    'fallback' => $message,
                    'text'     => $message,
                    'color'    => 'danger',
                    'fields'   => [
                        [
                            'title' => Lang::get('notifications.project'),
                            'value' => sprintf('<%s|%s>', url('project', $this->project_id), $this->project->name),
                            'short' => true
                        ]
                    ]
                ]
            ]
        ];

        return $payload;
    }
}
