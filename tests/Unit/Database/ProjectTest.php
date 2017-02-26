<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Database;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\App;
use Mockery as m;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Jobs\GenerateKey;
use REBELinBLUE\Deployer\Jobs\RegeneratePublicKey;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Ref;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
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

    /**
     * @covers ::boot
     */
    public function testBootBindsSavingEventToGenerateHash()
    {
        $expected = 'my-fake-token';

        $this->mockTokenGenerator($expected);

        /** @var Process $process */
        $process = m::mock(Process::class);
        $process->shouldNotReceive('setScript')->withAnyArgs();

        /** @var Project $project */
        $project = factory(Project::class)->make([
            'private_key' => 'a-private-key',
        ]);

        $project->public_key = 'a-public-key'; // Not "fillable"

        $this->assertEmpty($project->hash);

        $project->save();

        $this->assertSame($expected, $project->hash);
    }

    /**
     * @covers ::boot
     */
    public function testBootShouldNotRegenerateFieldsIfSet()
    {
        $expectedHash       = 'my-fake-token';
        $expectedPrivateKey = 'a-private-key';
        $expectedPublicKey  = 'a-public-key';

        /** @var Process $process */
        $process = m::mock(Process::class);
        $process->shouldNotReceive('setScript')->withAnyArgs();

        /** @var TokenGeneratorInterface $generator */
        $generator = m::mock(TokenGeneratorInterface::class);
        $generator->shouldNotReceive('generateRandom')->withAnyArgs();

        $this->app->instance(Process::class, $process);
        $this->app->instance(TokenGeneratorInterface::class, $generator);

        /** @var Project $project */
        $project = factory(Project::class)->make([
            'private_key' => $expectedPrivateKey,
        ]);

        // Not "fillable"
        $project->hash       = $expectedHash;
        $project->public_key = $expectedPublicKey;

        $this->assertSame($expectedHash, $project->hash);
        $this->assertSame($expectedPrivateKey, $project->private_key);
        $this->assertSame($expectedPublicKey, $project->public_key);

        $project->save();

        $this->assertSame($expectedHash, $project->hash);
        $this->assertSame($expectedPrivateKey, $project->private_key);
        $this->assertSame($expectedPublicKey, $project->public_key);
    }

    /**
     * @covers ::boot
     */
    public function testBootBindsSavingEventToGenerateKeypair()
    {
        $this->markTestIncomplete('broken right now');

        /** @var Project $project */
        $project = factory(Project::class)->make([
            'hash' => 'a-fake-hash',
        ]);

        $project->private_key = '';
        $project->public_key  = '';

        $this->expectsJobs(GenerateKey::class);

        $this->assertEmpty($project->private_key);
        $this->assertEmpty($project->public_key);

        $project->save();
    }

    /**
     * @covers ::boot
     */
    public function testBootBindsSavingEventToRegeneratePublicKeyWhenPrivateKeyProvided()
    {
        $this->markTestIncomplete('broken right now');

        $expectedPrivateKey = 'a-private-key';
        $expectedPublicKey  = 'a-public-key';

        /** @var Project $project */
        $project = factory(Project::class)->make([
            'hash'        => 'a-fake-hash',
            'private_key' => $expectedPrivateKey,
        ]);

        $this->assertSame($expectedPrivateKey, $project->private_key);
        $this->assertEmpty($project->public_key);

        $this->expectsJobs(RegeneratePublicKey::class);

        $project->save();

        $this->assertSame($expectedPrivateKey, $project->private_key);
        $this->assertSame($expectedPublicKey, $project->public_key);
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
