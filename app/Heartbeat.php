<?php

namespace REBELinBLUE\Deployer;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use REBELinBLUE\Deployer\Events\HeartbeatRecovered;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;

/**
 * Heartbeat model.
 */
class Heartbeat extends Model
{
    use SoftDeletes, BroadcastChanges;

    const OK       = 0;
    const UNTESTED = 1;
    const MISSING  = 2;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'pivot'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'interval', 'project_id'];

    /**
     * The fields which should be tried as Carbon instances.
     *
     * @var array
     */
    protected $dates = ['last_activity'];

    /**
     * Additional attributes to include in the JSON representation.
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
        'deploy_code' => 'boolean',
    ];

    /**
     * Belongs to relationship.
     *
     * @return Project
     */
    public function project()
    {
        return $this->belongsTo('REBELinBLUE\Deployer\Project');
    }

    /**
     * Override the boot method to bind model event listeners.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        // When first creating the model generate a webhook hash
        static::creating(function (Heartbeat $model) {
            if (!array_key_exists('hash', $model->attributes)) {
                $model->generateHash();
            }
        });
    }

    /**
     * Generates a hash for use in the webhook URL.
     *
     * @return void
     */
    public function generateHash()
    {
        $this->attributes['hash'] = Str::quickRandom(30);
    }

    /**
     * Define a accessor for the callback URL.
     *
     * @return string
     */
    public function getCallbackUrlAttribute()
    {
        return route('heartbeat', $this->hash);
    }

    /**
     * Updates the last_activity timestamp.
     *
     * @return bool
     */
    public function pinged()
    {
        $isHealthy = ($this->status === self::UNTESTED || $this->isHealthy());

        $this->status        = self::OK;
        $this->missed        = 0;
        $this->last_activity = $this->freshTimestamp();

        if (!$isHealthy) {
            event(new HeartbeatRecovered($this));
        }

        return $this->save();
    }

    /**
     * Determines whether the heartbeat is currently healthy.
     *
     * @return bool
     */
    public function isHealthy()
    {
        return ($this->status === self::OK);
    }
}
