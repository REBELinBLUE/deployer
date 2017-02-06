<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Traits;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Traits\HasTarget
 */
trait HasTarget
{
    /**
     * @covers ::target
     */
    protected function assertHasTarget($model)
    {
        $instance = new $model();
        $actual   = $instance->target();

        $this->assertInstanceOf(MorphTo::class, $actual);
        $this->assertMorphTo('target', $model);
    }
}
