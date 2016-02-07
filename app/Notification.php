<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Jobs\Notify;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;

/**
 * Notification model.
 */
class Notification extends Model
{
    use SoftDeletes, DispatchesJobs, BroadcastChanges;

    const SLACK   = 1;
    const HIPCHAT = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'channel', 'webhook', 'project_id', 'icon', 'failure_only', 'service'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'           => 'integer',
        'project_id'   => 'integer',
        'failure_only' => 'boolean',
        'service'      => 'integer',
    ];

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
     * Override the boot method to bind model event listeners.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        // When the notification has been saved queue a test
        static::saved(function (Notification $model) {
            $model->dispatch(new Notify($model, $model->testPayload()));
        });
    }

    /**
     * Generates a test payload for chat messaging.
     *
     * @return Message
     */
    public function testPayload()
    {
        $msg = new Message;
        $msg->setMessage(Lang::get('notifications.test_message'));

        return $msg;
    }
}
