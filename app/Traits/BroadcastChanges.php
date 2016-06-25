<?php

namespace REBELinBLUE\Deployer\Traits;

use REBELinBLUE\Deployer\Events\ModelChanged;
use REBELinBLUE\Deployer\Events\ModelCreated;
use REBELinBLUE\Deployer\Events\ModelTrashed;

/**
 * A trait to broadcast model changes.
 */
trait BroadcastChanges
{
    /**
     * Boot method to bind model event listeners.
     */
    public static function bootBroadcastChanges()
    {
        static::created(function ($model) {
            $channel = strtolower(class_basename(get_class($model)));
            event(new ModelCreated($model, $channel));
        });

        static::updated(function ($model) {
            $channel = strtolower(class_basename(get_class($model)));
            event(new ModelChanged($model, $channel));
        });

        static::deleted(function ($model) {
            $channel = strtolower(class_basename(get_class($model)));
            event(new ModelTrashed($model, $channel));
        });
    }
}
