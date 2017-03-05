<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;

/**
 * Notification channel.
 */
class Channel extends Model
{
    use SoftDeletes, BroadcastChanges, Notifiable;

    const EMAIL   = 'mail';
    const SLACK   = 'slack';
    const HIPCHAT = 'hipchat';
    const TWILIO  = 'twilio';
    const WEBHOOK = 'custom';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'project_id', 'type', 'config',
                           'on_deployment_success', 'on_deployment_failure',
                           'on_link_down', 'on_link_still_down', 'on_link_recovered',
                           'on_heartbeat_missing', 'on_heartbeat_still_missing', 'on_heartbeat_recovered', ];

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
        'id'                         => 'integer',
        'project_id'                 => 'integer',
        'config'                     => 'object',
        'on_deployment_success'      => 'boolean',
        'on_deployment_failure'      => 'boolean',
        'on_link_down'               => 'boolean',
        'on_link_still_down'         => 'boolean',
        'on_link_recovered'          => 'boolean',
        'on_heartbeat_missing'       => 'boolean',
        'on_heartbeat_still_missing' => 'boolean',
        'on_heartbeat_recovered'     => 'boolean',
    ];

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
     * Returns the email address to send the notification to.
     *
     * @return string|null
     */
    public function routeNotificationForMail()
    {
        if ($this->type === self::EMAIL) {
            return $this->config->email;
        }
    }

    /**
     * Returns the URL for the slack webhook.
     *
     * @return string|null
     */
    public function routeNotificationForSlack()
    {
        if ($this->type === self::SLACK) {
            return $this->config->webhook;
        }
    }

    /**
     * Returns the URL for the custom webhook.
     *
     * @return string|null
     */
    public function routeNotificationForWebhook()
    {
        if ($this->type === self::WEBHOOK) {
            return $this->config->url;
        }
    }

    /**
     * Returns the phone number for twilio notifications.
     *
     * @return string|null
     */
    public function routeNotificationForTwilio()
    {
        if ($this->type === self::TWILIO) {
            return $this->config->telephone;
        }
    }

    /**
     * Returns the room for the hipchat notifications.
     *
     * @return string|null
     */
    public function routeNotificationForHipchat()
    {
        if ($this->type === self::HIPCHAT) {
            return $this->config->room;
        }
    }

    /**
     * Scope a query to only include notifications for a specific event.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $event
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForEvent($query, $event)
    {
        return $query->where('on_' . $event, '=', true);
    }
}
