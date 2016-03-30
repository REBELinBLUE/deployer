<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Jobs\RequestProjectCheckUrl;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;

/**
 * The application's  url store for health check.
 */
class CheckUrl extends Model
{
    use SoftDeletes, BroadcastChanges, DispatchesJobs;

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
        'id'         => 'integer',
        'project_id' => 'integer',
        'is_report'  => 'boolean',
        'period'     => 'integer',
    ];

    /**
     * Override the boot method to bind model event listeners.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        // When first creating the model generate a webhook hash
        static::saved(function (CheckUrl $model) {
            if ($model->isDirty('url')) {
                $model->dispatch(new RequestProjectCheckUrl([$model]));
            }
        });
    }

    /**
     * Belongs to relationship.
     *
     * @return Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Generates a slack payload for the link failure.
     *
     * @return array
     */
    public function notificationPayload()
    {
        $message = Lang::get('checkUrls.message', ['link' => $this->title]);

        $payload = [
            'attachments' => [
                [
                    'fallback' => $message,
                    'text'     => $message,
                    'color'    => 'danger',
                    'fields'   => [
                        [
                            'title' => Lang::get('notifications.project'),
                            'value' => sprintf(
                                '<%s|%s>',
                                route('projects', ['id' => $this->project_id]),
                                $this->project->name
                            ),
                            'short' => true,
                        ],
                    ],
                ],
            ],
        ];

        return $payload;
    }
}
