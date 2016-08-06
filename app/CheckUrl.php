<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Jobs\RequestProjectCheckUrl;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;

/**
 * The application's url store for health check.
 *
 * @property integer $id
 * @property string $title
 * @property string $url
 * @property integer $project_id
 * @property integer $period
 * @property boolean $is_report
 * @property boolean $last_status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property-read Project $project
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
     * @dispatches RequestProjectCheckUrl
     */
    public static function boot()
    {
        parent::boot();

        // When saving the model, if the URL has changed we need to test it
        static::saved(function (CheckUrl $model) {
            if (is_null($model->last_status)) {
                $model->dispatch(new RequestProjectCheckUrl([$model]));
            }
        });
    }

    /**
     * Define a mutator to set the status to untested if the URL changes.
     *
     * @param string $value
     */
    public function setUrlAttribute($value)
    {
        if (!array_key_exists('url', $this->attributes) || $value !== $this->attributes['url']) {
            $this->attributes['last_status'] = null;
        }

        $this->attributes['url'] = $value;
    }

    /**
     * Belongs to relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
                    'footer' => Lang::get('app.name'),
                    'ts'     => time(),
                ],
            ],
        ];

        return $payload;
    }
}
