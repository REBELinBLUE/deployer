<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Database;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Ref;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\BroadcastChanges;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Project
 * @group slow
 */
class ProjectTest extends TestCase
{
    use DatabaseMigrations, BroadcastChanges;

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

    /**
     * @dataProvider provideStatusCounts
     * @covers ::heartbeatsStatus
     */
    public function testHeartbeatsStatus($healthy, $missing)
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        factory(Heartbeat::class, $healthy)->states('healthy')->create([
            'project_id' => $project->id,
        ]);

        factory(Heartbeat::class, $missing)->states('missing')->create([
            'project_id' => $project->id,
        ]);

        $actual = $project->heartbeatsStatus();

        $this->assertStatusArray($healthy, $missing, $actual);
    }

    /**
     * @dataProvider provideStatusCounts
     * @covers ::applicationCheckUrlStatus
     */
    public function testApplicationCheckUrlStatus($healthy, $missing)
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        factory(CheckUrl::class, $healthy)->states('healthy')->create([
            'project_id' => $project->id,
        ]);

        factory(CheckUrl::class, $missing)->states('down')->create([
            'project_id' => $project->id,
        ]);

        $actual = $project->applicationCheckUrlStatus();

        $this->assertStatusArray($healthy, $missing, $actual);
    }

    public function provideStatusCounts()
    {
        return $this->fixture('Project')['health'];
    }

    /**
     * @covers ::getBranchesAttribute
     */
    public function testGetBranchesAttribute()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $this->populateRefs($project);

        $expected = $this->fixture('Ref')['ordered']['branches'];

        $this->assertSame($expected, array_values($project->getBranchesAttribute()->toArray()));
        $this->assertSame($expected, array_values($project->branches->toArray()));
    }

    /**
     * @covers ::getTagsAttribute
     */
    public function testGetTagsAttribute()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $this->populateRefs($project);

        $expected = $this->fixture('Ref')['ordered']['tags'];

        $this->assertSame($expected, array_values($project->getTagsAttribute()->toArray()));
        $this->assertSame($expected, array_values($project->tags->toArray()));
    }

    private function assertStatusArray($healthy, $missing, $actual)
    {
        $this->assertInternalType('array', $actual);
        $this->assertArrayHasKey('missed', $actual);
        $this->assertArrayHasKey('length', $actual);
        $this->assertSame($healthy + $missing, $actual['length']);
        $this->assertSame($missing, $actual['missed']);
    }

    private function populateRefs($project)
    {
        foreach ($this->fixture('Ref')['dataset'] as $ref) {
            factory(Ref::class)->create(array_merge($ref, [
                'project_id' => $project->id,
            ]));
        }
    }
}
