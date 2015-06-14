<?php

namespace App;

use App\Traits\BroadcastChanges;
use App\Jobs\Notify;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Lang;

/**
 * Notification model.
 */
class Notification extends Model
{
    use SoftDeletes, DispatchesJobs, BroadcastChanges;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'channel', 'webhook', 'project_id', 'icon'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

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
     * Override the boot method to bind model event listeners.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        // When the notification has been saved queue a test
        static::saved(function (Notification $model) {
            $model->dispatch(new Notify(
                $model,
                $model->testPayload()
            ));
        });
    }

    /**
     * Generates a test payload for Slack.
     *
     * @return array
     */
    public function testPayload()
    {
        return [
            'text' => Lang::get('notifications.test_message'),
        ];
    }
}
