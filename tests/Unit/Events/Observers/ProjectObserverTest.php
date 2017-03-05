<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events\Observers;

use Mockery as m;
use REBELinBLUE\Deployer\Events\Observers\ProjectObserver;
use REBELinBLUE\Deployer\Jobs\GenerateKey;
use REBELinBLUE\Deployer\Jobs\RegeneratePublicKey;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\Observers\ProjectObserver
 */
class ProjectObserverTest extends TestCase
{
    /**
     * @covers ::creating
     */
    public function testCreatingGeneratesKeypair()
    {
        /** @var Project $project */
        $project = factory(Project::class)->make([
            'group_id'    => 1,
            'hash'        => 'a-fake-hash',
            'private_key' => '',
            'public_key'  => '',
        ]);

        $this->expectsJobs(GenerateKey::class);
        $this->doesntExpectJobs(RegeneratePublicKey::class);

        $observer = new ProjectObserver();
        $observer->creating($project);
    }

    /**
     * @covers ::creating
     */
    public function testCreatingRegeneratesPublicKeyWhenPrivateKeyProvided()
    {
        $expectedPrivateKey = 'a-private-key';

        /** @var Project $project */
        $project = factory(Project::class)->make([
            'group_id'    => 1,
            'hash'        => 'a-fake-hash',
            'private_key' => $expectedPrivateKey,
            'public_key'  => '',
        ]);

        $this->assertSame($expectedPrivateKey, $project->private_key);
        $this->assertEmpty($project->public_key);

        $this->expectsJobs(RegeneratePublicKey::class);
        $this->doesntExpectJobs(GenerateKey::class);

        $observer = new ProjectObserver();
        $observer->creating($project);

        $this->assertSame($expectedPrivateKey, $project->private_key);
    }

    /**
     * @covers ::creating
     */
    public function testCreatingGeneratesHash()
    {
        $expected = 'my-fake-token';

        $this->mockTokenGenerator($expected);

        /** @var Project $project */
        $project = factory(Project::class)->make([
            'group_id'    => 1,
            'private_key' => 'a-private-key',
            'public_key'  => 'a-public-key',
        ]);

        $this->assertEmpty($project->hash);

        $observer = new ProjectObserver();
        $observer->creating($project);

        $this->assertSame($expected, $project->hash);
    }

    /**
     * @covers ::creating
     */
    public function testCreatingShouldNotRegenerateFieldsIfSet()
    {
        $expectedHash       = 'my-fake-token';
        $expectedPrivateKey = 'a-private-key';
        $expectedPublicKey  = 'a-public-key';

        /** @var TokenGeneratorInterface $generator */
        $generator = m::mock(TokenGeneratorInterface::class);
        $generator->shouldNotReceive('generateRandom');

        $this->app->instance(TokenGeneratorInterface::class, $generator);

        /** @var Project $project */
        $project = factory(Project::class)->make([
            'group_id'    => 1,
            'private_key' => $expectedPrivateKey,
            'hash'        => $expectedHash,
            'public_key'  => $expectedPublicKey,
        ]);

        $this->assertSame($expectedHash, $project->hash);
        $this->assertSame($expectedPrivateKey, $project->private_key);
        $this->assertSame($expectedPublicKey, $project->public_key);

        $this->doesntExpectJobs([GenerateKey::class, RegeneratePublicKey::class]);

        $observer = new ProjectObserver();
        $observer->creating($project);

        $this->assertSame($expectedHash, $project->hash);
        $this->assertSame($expectedPrivateKey, $project->private_key);
        $this->assertSame($expectedPublicKey, $project->public_key);
    }

    /**
     * @covers ::updating
     */
    public function testUpdatingRegeneratesPublicKeyWhenPrivateKeyProvided()
    {
        $expectedPrivateKey = 'a-private-key';

        /** @var Project $project */
        $project = factory(Project::class)->make([
            'group_id'    => 1,
            'hash'        => 'a-fake-hash',
            'private_key' => $expectedPrivateKey,
            'public_key'  => '',
        ]);

        $this->assertSame($expectedPrivateKey, $project->private_key);
        $this->assertEmpty($project->public_key);

        $this->expectsJobs(RegeneratePublicKey::class);

        $observer = new ProjectObserver();
        $observer->updating($project);

        $this->assertSame($expectedPrivateKey, $project->private_key);
    }

    /**
     * @covers ::updating
     */
    public function testUpdatingShouldNotRegeneratePublicKeyIfSet()
    {
        $expectedPrivateKey = 'a-private-key';
        $expectedPublicKey  = 'a-public-key';

        /** @var Project $project */
        $project = factory(Project::class)->make([
            'group_id'    => 1,
            'hash'        => 'a-fake-hash',
            'private_key' => $expectedPrivateKey,
            'public_key'  => $expectedPublicKey,
        ]);

        $this->assertSame($expectedPrivateKey, $project->private_key);
        $this->assertSame($expectedPublicKey, $project->public_key);

        $this->doesntExpectJobs(RegeneratePublicKey::class);

        $observer = new ProjectObserver();
        $observer->updating($project);

        $this->assertSame($expectedPrivateKey, $project->private_key);
        $this->assertSame($expectedPublicKey, $project->public_key);
    }
}
