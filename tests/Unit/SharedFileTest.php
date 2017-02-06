<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use REBELinBLUE\Deployer\SharedFile;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\HasTarget;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\SharedFile
 */
class SharedFileTest extends TestCase
{
    use TestsModel, HasTarget;

    /**
     * @covers \REBELinBLUE\Deployer\Traits\HasTarget
     */
    public function testTarget()
    {
        $this->assertHasTarget(SharedFile::class);
    }
}
