<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Database\Eloquent\Relations\HasMany;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Group
 */
class GroupTest extends TestCase
{
    /**
     * @covers ::projects
     */
    public function testProject()
    {
        $group  = new Group();
        $actual = $group->projects();

        // TODO: Check the order by?
        $this->assertInstanceOf(HasMany::class, $actual);
        $this->assertSame('group_id', $actual->getForeignKeyName());
    }
}
