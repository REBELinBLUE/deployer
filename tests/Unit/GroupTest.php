<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Database\Eloquent\Relations\HasMany;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Group
 */
class GroupTest extends TestCase
{
    use TestsModel;

    /**
     * @covers ::projects
     */
    public function testProject()
    {
        $group  = new Group();
        $actual = $group->projects();

        // TODO: Check the order by?
        $this->assertInstanceOf(HasMany::class, $actual);
        $this->assertHasMany('projects', Group::class);
    }
}
