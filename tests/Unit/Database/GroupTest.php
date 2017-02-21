<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Database;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\BroadcastChanges;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Group
 * @group slow
 */
class GroupTest extends TestCase
{
    use DatabaseMigrations, BroadcastChanges;

    /**
     * @covers ::getProjectCountAttribute
     */
    public function testGetProjectCountAttribute()
    {
        $expected = 5;

        /** @var Group $group */
        $group = factory(Group::class)->create();

        factory(Project::class, $expected)->create([
            'group_id' => $group->id,
        ]);

        $this->assertSame($expected, $group->project_count);
        $this->assertSame($expected, $group->getProjectCountAttribute());
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastCreatedEvent()
    {
        $this->assertBroadcastCreatedEvent(Group::class);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastUpdatedEvent()
    {
        $this->assertBroadcastUpdatedEvent(Group::class, [
            'name' => 'Group',
        ], [
            'name' => 'Renamed Group',
        ]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastTrashedEvent()
    {
        $this->assertBroadcastTrashedEvent(Group::class);
    }
}
