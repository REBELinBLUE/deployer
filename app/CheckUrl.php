<?php

namespace App;

use App\Traits\BroadcastChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

/**
 * The application's  url store for health check.
 */
class CheckUrl extends Model
{
    use SoftDeletes, BroadcastChanges;

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
