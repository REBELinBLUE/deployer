<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\HasTarget;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;
use REBELinBLUE\Deployer\Variable;

/*
 * @coversDefaultClass \REBELinBLUE\Deployer\Variable
 */
class VariableTest extends TestCase
{
    use TestsModel, HasTarget;

    /**
     * @covers \REBELinBLUE\Deployer\Traits\HasTarget
     */
    public function testTarget()
    {
        $this->assertHasTarget(Variable::class);
    }
}
