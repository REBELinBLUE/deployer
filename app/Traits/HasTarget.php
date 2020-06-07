<?php

namespace REBELinBLUE\Deployer\Traits;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A trait for target polymorphic relationship.
 */
trait HasTarget
{
    /**
     * One-to-one to polymorphic relationship.
     *
     * @return MorphTo
     */
    public function target()
    {
        return $this->morphTo();
    }
}
