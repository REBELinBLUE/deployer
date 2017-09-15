<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;

class ProjectServer extends Model
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
    protected $fillable = ['user', 'path', 'project_id', 'server_id', 'deploy_code'];

    protected $appends = ['server'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'project_id'  => 'integer',
        'server_id'   => 'integer',
        'status'      => 'integer',
        'deploy_code' => 'boolean',
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

    public function getServerAttribute()
    {
        return $this->server()->first();
    }

    /**
     * Belongs to relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function server()
    {
        return $this->belongsTo(Server::class);
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
     * The server path without a trailing slash.
     *
     * @return string
     */
    public function getCleanPathAttribute()
    {
        if (empty($this->path)) {
            return preg_replace('#/$#', '', $this->server->path);
        }

        // FIXME: Clean this up
        return preg_replace('#/{2,}#', '/', preg_replace('#/$#', '', $this->server->path . '/' . $this->path));
    }

    /**
     * Returns the user to actually connect as.
     *
     * @return string
     */
    public function getConnectAsAttribute()
    {
        if (empty($this->user)) {
            return $this->server->user;
        }

        return $this->user;
    }
}
