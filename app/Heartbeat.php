<?php namespace App;

use Lang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Heartbeat model
 */
class Heartbeat extends Model
{
    use SoftDeletes;

    const OK       = 0;
    const UNTESTED = 1;
    const MISSING  = 2;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['project_id', 'created_at', 'updated_at', 'deleted_at', 'pivot'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'interval', 'project_id'];

    /**
     * The fields which should be tried as Carbon instances
     *
     * @var array
     */
    protected $dates = ['last_activity'];

    /**
     * Additional attributes to include in the JSON representation
     *
     * @var array
     */
    protected $appends = ['callback_url'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'status'      => 'integer',
        'deploy_code' => 'boolean'
    ];

    /**
     * Belongs to relationship
     *
     * @return Project
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * Override the boot method to bind model event listeners
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        // When first creating the model generate a webhook hash
        static::creating(function ($model) {
            if (!array_key_exists('hash', $model->attributes)) {
                $model->generateHash();
            }
        });
    }

    /**
     * Generates a hash for use in the webhook URL
     *
     * @return void
     */
    public function generateHash()
    {
        $this->attributes['hash'] = Str::quickRandom(30);
    }

    /**
     * Define a mutator for the callback URL
     *
     * @return string
     * @todo  Shouldn't this be a presenter?
     */
    public function getCallbackUrlAttribute()
    {
        return route('heartbeat', $this->hash);
    }

    /**
     * Updates the last_activity timestamp
     * 
     * @return boolean
     */
    public function pinged()
    {
        $this->status        = self::OK;
        $this->last_activity = $this->freshTimestamp();

        return $this->save();
    }

    /**
     * Generates a slack payload for the heartbeat failuyre
     *
     * @return array
     */
    public function notificationPayload()
    {
        $message = 'We have not heard from JOB in a while!'; //Lang::get('notifications.failed_message');

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
                        ], [
                            'title' => Lang::get('heartbeats.checkin'),
                            'value' => '10',
                            'short' => true
                        ]
                    ]
                ]
            ]
        ];

        return $payload;
    }
}
