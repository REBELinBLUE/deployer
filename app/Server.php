<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;

/**
 * Server model.
 * @todo See if there is a cleaner way to do the mutators as they are all very similar
 */
class Server extends Model
{
    use SoftDeletes, BroadcastChanges;

    const SUCCESSFUL = 0;
    const UNTESTED   = 1;
    const FAILED     = 2;
    const TESTING    = 3;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'pivot', 'project'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'user', 'ip_address', 'project_id', 'path',
                           'status', 'deploy_code', 'port', 'order', ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'project_id'  => 'integer',
        'status'      => 'integer',
        'deploy_code' => 'boolean',
        'port'        => 'integer',
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
     * Determines whether the server is currently being testing.
     *
     * @return bool
     */
    public function isTesting()
    {
        return ($this->status === self::TESTING);
    }

    /**
     * Define a mutator for the user, if it has changed or has
     * not previously been set also set the status to untested.
     *
     * @param  string $value
     * @return void
     */
    public function setUserAttribute($value)
    {
        if (!array_key_exists('user', $this->attributes) || $value !== $this->attributes['user']) {
            $this->attributes['status'] = self::UNTESTED;
        }

        $this->attributes['user'] = $value;
    }

    /**
     * Define a mutator for the path, if it has changed or has
     * not previously been set also set the status to untested.
     *
     * @param  string $value
     * @return void
     */
    public function setPathAttribute($value)
    {
        if (!array_key_exists('path', $this->attributes) || $value !== $this->attributes['path']) {
            $this->attributes['status'] = self::UNTESTED;
        }

        $this->attributes['path'] = $value;
    }

    /**
     * Define a mutator for the IP Address, if it has changed or
     * has not previously been set also set the status to untested.
     *
     * @param  string $value
     * @return void
     */
    public function setIpAddressAttribute($value)
    {
        if (!array_key_exists('ip_address', $this->attributes) || $value !== $this->attributes['ip_address']) {
            $this->attributes['status'] = self::UNTESTED;
        }

        $this->attributes['ip_address'] = $value;
    }

    /**
     * Define a mutator for the port, if it has changed or
     * has not previously been set also set the status to untested.
     *
     * @param  string $value
     * @return void
     */
    public function setPortAttribute($value)
    {
        if (!array_key_exists('port', $this->attributes) || (int) $value !== (int) $this->attributes['port']) {
            $this->attributes['status'] = self::UNTESTED;
        }

        $this->attributes['port'] = $value;
    }

    /**
     * The server path without a trailing slash.
     *
     * @param  string  $value
     * @return string
     */
    public function getCleanPathAttribute()
    {
        return preg_replace('#/$#', '', $this->path);
    }
}
