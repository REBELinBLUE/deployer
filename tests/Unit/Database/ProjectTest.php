<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Database;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Project
 * @group slow
 */
class ProjectTest extends TestCase
{
    use DatabaseMigrations, BroadcastChangesTests;

    /**
     * @covers ::getGroupNameAttribute
     */
    public function testGetGroupNameAttribute()
    {
        $expected = 'a-test-group';

        /** @var Group $group */
        $group = factory(Group::class)->create([
            'name' => $expected,
        ]);

        /** @var Project $project */
        $project = factory(Project::class)->create([
            'group_id' => $group->id,
        ]);

        $this->assertSame($expected, $project->getGroupNameAttribute());
        $this->assertSame($expected, $project->group_name);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastCreatedEvent()
    {
        $this->assertBroadcastCreatedEvent(Project::class);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastUpdatedEvent()
    {
        $this->assertBroadcastUpdatedEvent(Project::class, [
            'status' => Project::NOT_DEPLOYED,
        ], [
            'status' => Project::FINISHED,
        ]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastTrashedEvent()
    {
        $this->assertBroadcastTrashedEvent(Project::class);
    }
}
