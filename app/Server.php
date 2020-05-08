<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;

/**
 * Server model.
 */
class Server extends Model
{
    use SoftDeletes, BroadcastChanges;

    public const SUCCESSFUL = 0;
    public const UNTESTED   = 1;
    public const FAILED     = 2;
    public const TESTING    = 3;

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
     * @return BelongsTo
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
    public function isTesting(): bool
    {
        return ($this->status === self::TESTING);
    }

    /**
     * Define a mutator for the user, if it has changed or has
     * not previously been set also set the status to untested.
     *
     * @param string $value
     */
    public function setUserAttribute(string $value): void
    {
        $this->setAttributeStatusUntested('user', $value);
    }

    /**
     * Define a mutator for the path, if it has changed or has
     * not previously been set also set the status to untested.
     *
     * @param string $value
     */
    public function setPathAttribute(string $value): void
    {
        $this->setAttributeStatusUntested('path', $value);
    }

    /**
     * Define a mutator for the IP Address, if it has changed or
     * has not previously been set also set the status to untested.
     *
     * @param string $value
     */
    public function setIpAddressAttribute(string $value): void
    {
        $this->setAttributeStatusUntested('ip_address', $value);
    }

    /**
     * Define a mutator for the port, if it has changed or
     * has not previously been set also set the status to untested.
     *
     * @param string $value
     */
    public function setPortAttribute(string $value): void
    {
        $this->setAttributeStatusUntested('port', (int) $value);
    }

    /**
     * The server path without a trailing slash.
     *
     * @return string
     */
    public function getCleanPathAttribute(): string
    {
        return preg_replace('#/$#', '', $this->path);
    }

    /**
     * Updates the attribute value and if it has changed set the server status to untested.
     *
     * @param string $attribute
     * @param mixed  $value
     */
    private function setAttributeStatusUntested(string $attribute, $value): void
    {
        if (!array_key_exists($attribute, $this->attributes) || $value !== $this->attributes[$attribute]) {
            $this->attributes['status']      = self::UNTESTED;
            $this->attributes['connect_log'] = null;
        }

        $this->attributes[$attribute] = $value;
    }
}
