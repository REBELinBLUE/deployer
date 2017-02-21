<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\HasTarget;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\ConfigFile
 */
class ConfigFileTest extends TestCase
{
    use TestsModel, HasTarget;

    /**
     * @covers \REBELinBLUE\Deployer\Traits\HasTarget
     */
    public function testTarget()
    {
        $this->assertHasTarget(ConfigFile::class);
    }
}
