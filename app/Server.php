<?php

namespace App;

use App\Events\ModelChanged;
use App\Events\ModelCreated;
use App\Events\ModelTrashed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Server model.
 */
class Server extends Model
{
    use SoftDeletes;

    const SUCCESSFUL = 0;
    const UNTESTED   = 1;
    const FAILED     = 2;
    const TESTING    = 3;

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
    protected $fillable = ['name', 'user', 'ip_address', 'project_id', 'path', 'status', 'deploy_code', 'port'];

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
     * Override the boot method to bind model event listeners.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::updated(function (Server $model) {
            event(new ModelChanged($model, 'server'));
        });

        static::created(function (Server $model) {
            event(new ModelCreated($model, 'server'));
        });

        static::deleted(function (Server $model) {
            event(new ModelTrashed($model, 'server'));
        });
    }

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
     * Define a mutator for the IP Address, if it has changed or
     * has not previously been set also set the status to untested.
     *
     * @param string $value
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
     * @param string $value
     * @return void
     */
    public function setPortAttribute($value)
    {
        if (!array_key_exists('port', $this->attributes) || $value !== $this->attributes['port']) {
            $this->attributes['status'] = self::UNTESTED;
        }

        $this->attributes['port'] = $value;
    }
}
