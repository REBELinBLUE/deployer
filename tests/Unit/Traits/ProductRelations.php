<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Traits\ProjectRelations
 */
trait ProductRelations
{
    protected function assertHasProjectRelations($model)
    {
        $this->assertHasCommands($model);
        $this->assertHasVariables($model);
        $this->assertHasSharedFiles($model);
        $this->assertHasConfigFiles($model);
    }

    /**
     * @covers ::commands
     */
    protected function assertHasCommands($model)
    {
        $instance = new $model();
        $actual   = $instance->commands();

        // TODO: Check the order by?
        $this->assertInstanceOf(MorphMany::class, $actual);
        $this->assertMorphMany('commands', $model);
    }

    /**
     * @covers ::variables
     */
    protected function assertHasVariables($model)
    {
        $instance = new $model();
        $actual   = $instance->variables();

        // TODO: Check the order by?
        $this->assertInstanceOf(MorphMany::class, $actual);
        $this->assertMorphMany('variables', $model);
    }

    /**
     * @covers ::sharedFiles
     */
    protected function assertHasSharedFiles($model)
    {
        $instance = new $model();
        $actual   = $instance->sharedFiles();

        // TODO: Check the order by?
        $this->assertInstanceOf(MorphMany::class, $actual);
        $this->assertMorphMany('sharedFiles', $model);
    }

    /**
     * @covers ::variables
     */
    protected function assertHasConfigFiles($model)
    {
        $instance = new $model();
        $actual   = $instance->configFiles();

        // TODO: Check the order by?
        $this->assertInstanceOf(MorphMany::class, $actual);
        $this->assertMorphMany('configFiles', $model);
    }
}
