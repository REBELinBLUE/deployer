<?php

namespace REBELinBLUE\Deployer\Traits;

/**
 * A trait for target polymorphic relationship.
 */
trait HasTarget
{
    /**
     * One-to-one to polymorphic relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function target()
    {
        return $this->morphTo();
    }
}
