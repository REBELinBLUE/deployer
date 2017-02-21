<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;
use REBELinBLUE\Deployer\Traits\HasTarget;

/**
 * The command model.
 */
class Command extends Model
{
    use SoftDeletes, BroadcastChanges, HasTarget;

    const BEFORE_CLONE    = 1;
    const DO_CLONE        = 2;
    const AFTER_CLONE     = 3;
    const BEFORE_INSTALL  = 4;
    const DO_INSTALL      = 5;
    const AFTER_INSTALL   = 6;
    const BEFORE_ACTIVATE = 7;
    const DO_ACTIVATE     = 8;
    const AFTER_ACTIVATE  = 9;
    const BEFORE_PURGE    = 10;
    const DO_PURGE        = 11;
    const AFTER_PURGE     = 12;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'user', 'script', 'target_type', 'target_id',
                           'step', 'order', 'optional', 'default_on', ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'deleted_at', 'updated_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'step'       => 'integer',
        'optional'   => 'boolean',
        'default_on' => 'boolean',
        'order'      => 'integer',
    ];

    /**
     * Belongs to many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function servers()
    {
        return $this->belongsToMany(Server::class)
                    ->orderBy('order', 'ASC');
    }
}
