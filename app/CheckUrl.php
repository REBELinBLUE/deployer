<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;

/**
 * The application's url store for health check.
 */
class CheckUrl extends Model
{
    use SoftDeletes, BroadcastChanges;

    const ONLINE   = 0;
    const UNTESTED = 1;
    const OFFLINE  = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'url', 'project_id', 'period'];

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
        'missed'     => 'integer',
        'period'     => 'integer',
        'status'     => 'integer',
    ];

    /**
     * The fields which should be treated as Carbon instances.
     *
     * @var array
     */
    protected $dates = ['last_seen'];

    /**
     * Define a mutator to set the status to untested if the URL changes.
     *
     * @param string $value
     */
    public function setUrlAttribute($value)
    {
        if (!array_key_exists('url', $this->attributes) || $value !== $this->attributes['url']) {
            $this->attributes['status']    = self::UNTESTED;
            $this->attributes['last_log']  = null;
            $this->attributes['last_seen'] = null;
        }

        $this->attributes['url'] = $value;
    }

    /**
     * Flags the link as healthy.
     *
     * @return bool
     */
    public function online()
    {
        $this->status    = self::ONLINE;
        $this->missed    = 0;
        $this->last_seen = $this->freshTimestamp();
    }

    /**
     * Flags the link as down.
     *
     * @return bool
     */
    public function offline()
    {
        $this->status = self::OFFLINE;
        $this->missed = $this->missed + 1;
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
     * Determines whether the URL is currently online.
     *
     * @return bool
     */
    public function isHealthy()
    {
        return ($this->status === self::ONLINE);
    }
}
