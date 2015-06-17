<?php

namespace App\Traits;

use App\Events\ModelChanged;
use App\Events\ModelCreated;
use App\Events\ModelTrashed;

/**
 * A trait to broadcast model changes.
 */
trait BroadcastChanges
{
    /**
     * Override the boot method to bind model event listeners.
     *
     * @return void
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
