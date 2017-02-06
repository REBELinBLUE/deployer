<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Traits;

use REBELinBLUE\Deployer\Events\ModelChanged;
use REBELinBLUE\Deployer\Events\ModelCreated;
use REBELinBLUE\Deployer\Events\ModelTrashed;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Traits\BroadcastChanges
 */
trait BroadcastChanges
{
    /**
     * @covers ::bootBroadcastChanges
     */
    protected function assertBroadcastCreatedEvent($class)
    {
        $this->withoutJobs();
        $this->expectsEvents(ModelCreated::class);

        factory($class)->create();
    }

    /**
     * @covers ::bootBroadcastChanges
     */
    protected function assertBroadcastUpdatedEvent($class, array $defaults = [], array $changes = [])
    {
        $this->withoutJobs();
        $this->expectsEvents(ModelCreated::class);
        $this->expectsEvents(ModelChanged::class);

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = factory($class)->create($defaults);

        // Can't use ::update because we want to test with all fields not just those marked as fillable
        foreach ($changes as $field => $change) {
            $model->setAttribute($field, $change);
        }

        $model->save();
    }

    /**
     * @covers ::bootBroadcastChanges
     */
    protected function assertBroadcastTrashedEvent($class)
    {
        $this->withoutJobs();
        $this->expectsEvents(ModelCreated::class);
        $this->expectsEvents(ModelTrashed::class);

        factory($class)->create()->delete();
    }
}
