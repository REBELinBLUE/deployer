<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;

/**
 * Server model.
 */
class Server extends Model
{
    use SoftDeletes, BroadcastChanges;

    const SUCCESSFUL = 0;
    const UNTESTED   = 1;
    const FAILED     = 2;
    const TESTING    = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'user', 'ip_address', 'project_id', 'path',
                           'status', 'deploy_code', 'port', 'order', ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'pivot', 'project'];

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
     * @param string $value
     */
    public function setUserAttribute($value)
    {
        $this->setAttributeStatusUntested('user', $value);
    }

    /**
     * Define a mutator for the path, if it has changed or has
     * not previously been set also set the status to untested.
     *
     * @param string $value
     */
    public function setPathAttribute($value)
    {
        $this->setAttributeStatusUntested('path', $value);
    }

    /**
     * Define a mutator for the IP Address, if it has changed or
     * has not previously been set also set the status to untested.
     *
     * @param string $value
     */
    public function setIpAddressAttribute($value)
    {
        $this->setAttributeStatusUntested('ip_address', $value);
    }

    /**
     * Define a mutator for the port, if it has changed or
     * has not previously been set also set the status to untested.
     *
     * @param string $value
     */
    public function setPortAttribute($value)
    {
        $this->setAttributeStatusUntested('port', (int) $value);
    }

    /**
     * Updates the attribute value and if it has changed set the server status to untested.
     *
     * @param string $attribute
     * @param mixed  $value
     */
    private function setAttributeStatusUntested($attribute, $value)
    {
        if (!array_key_exists($attribute, $this->attributes) || $value !== $this->attributes[$attribute]) {
            $this->attributes['status'] = self::UNTESTED;
        }

        $this->attributes[$attribute] = $value;
    }

    /**
     * The server path without a trailing slash.
     *
     * @return string
     */
    public function getCleanPathAttribute()
    {
        return preg_replace('#/$#', '', $this->path);
    }
}
