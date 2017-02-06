<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Mockery as m;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\stubs\Project as StubProject;
use REBELinBLUE\Deployer\Tests\Unit\Traits\ProductRelations;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;
use REBELinBLUE\Deployer\View\Presenters\ProjectPresenter;
use RuntimeException;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Project
 */
class ProjectTest extends TestCase
{
    use TestsModel, ProductRelations;

    /**
     * @covers ::getPresenter
     */
    public function testGetPresenter()
    {
        $project   = new Project();
        $presenter = $project->getPresenter();

        $this->assertInstanceOf(ProjectPresenter::class, $presenter);
        $this->assertSame($project, $presenter->getObject());
    }

    /**
     * @covers ::generateHash
     */
    public function testGenerateHash()
    {
        $expected = 'a-random-project-token-hash';

        $this->mockTokenGenerator($expected);

        $project = new Project();
        $project->generateHash();

        $this->assertSame($expected, $project->hash);
    }

    /**
     * @covers ::getWebhookUrlAttribute
     */
    public function testGetWebhookUrlAttribute()
    {
        $hash     = 'a-url-safe-hash';
        $expected = 'http://localhost/deploy/' . $hash;

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('webhook.deploy', $hash, true)
             ->andReturn($expected);

        App::instance('url', $mock);

        $this->mockTokenGenerator($hash);

        $project = new Project();
        $project->generateHash();

        $this->assertSame($expected, $project->webhook_url);
        $this->assertSame($expected, $project->getWebhookUrlAttribute());
    }

    /**
     * @dataProvider provideStatuses
     */
    public function testIsDeploying($status, $expected)
    {
        $project         = new Project();
        $project->status = $status;

        $this->assertSame($expected, $project->isDeploying());
    }

    public function provideStatuses()
    {
        return $this->fixture('Project')['running'];
    }

    /**
     * @dataProvider provideRepositoryUrls
     * @covers ::mirrorPath
     */
    public function testMirrorPath($repository, $expected)
    {
        $project             = new Project();
        $project->repository = $repository;

        $folder = storage_path('app/mirrors/');
        $actual = $project->mirrorPath();

        $this->assertSame($expected, str_replace($folder, '', $actual));
        $this->assertStringStartsWith($folder, $actual);
    }

    public function provideRepositoryUrls()
    {
        return $this->fixture('Project')['repository_mirror_folder'];
    }

    /**
     * @dataProvider provideAccessDetails
     * @covers ::accessDetails
     */
    public function testAccessDetails($repository, $scheme, $user, $domain, $port, $reference)
    {
        $project             = new Project();
        $project->repository = $repository;

        $actual = $project->accessDetails();

        $this->assertInternalType('array', $actual);
        $this->assertCount(5, $actual);
        $this->assertArrayHasKey('scheme', $actual);
        $this->assertArrayHasKey('user', $actual);
        $this->assertArrayHasKey('domain', $actual);
        $this->assertArrayHasKey('port', $actual);
        $this->assertArrayHasKey('reference', $actual);

        $this->assertSame($scheme, $actual['scheme']);
        $this->assertSame($user, $actual['user']);
        $this->assertSame($domain, $actual['domain']);
        $this->assertSame($port, $actual['port']);
        $this->assertSame($reference, $actual['reference']);
    }

    public function provideAccessDetails()
    {
        return $this->fixture('Project')['access_details'];
    }

    /**
     * @dataProvider provideMalformedRepositoryUrl
     * @covers ::accessDetails
     */
    public function testAccessDetailHandlesUnknown($repository)
    {
        $project             = new Project();
        $project->repository = $repository;

        $actual = $project->accessDetails();

        $this->assertInternalType('array', $actual);
        $this->assertCount(0, $actual);
    }

    public function provideMalformedRepositoryUrl()
    {
        return array_chunk($this->fixture('Project')['malformed_url'], 1);
    }

    /**
     * @dataProvider provideRepositoryPath
     * @covers ::getRepositoryPathAttribute
     */
    public function testGetRepositoryPathAttribute($repository, $expected)
    {
        $project             = new Project();
        $project->repository = $repository;

        $this->assertSame($expected, $project->getRepositoryPathAttribute());
        $this->assertSame($expected, $project->repository_path);
    }

    public function provideRepositoryPath()
    {
        return $this->fixture('Project')['paths'];
    }

    /**
     * @dataProvider provideMalformedRepositoryUrl
     * @covers ::getRepositoryPathAttribute
     */
    public function testGetRepositoryPathAttributeHandlesUnknown($repository)
    {
        $project             = new Project();
        $project->repository = $repository;

        $this->assertFalse($project->getRepositoryPathAttribute());
        $this->assertFalse($project->repository_path);
    }

    /**
     * @dataProvider provideRepositoryUrl
     * @covers ::getRepositoryUrlAttribute
     */
    public function testRepositoryUrlAttribute($repository, $expected)
    {
        $project             = new Project();
        $project->repository = $repository;

        $this->assertSame($expected, $project->getRepositoryUrlAttribute());
        $this->assertSame($expected, $project->repository_url);
    }

    public function provideRepositoryUrl()
    {
        return $this->fixture('Project')['repo_url_to_web_url'];
    }

    /**
     * @dataProvider provideMalformedRepositoryUrl
     * @covers ::getRepositoryUrlAttribute
     */
    public function testRepositoryUrlAttributeHandlesUnknown($repository)
    {
        $project             = new Project();
        $project->repository = $repository;

        $this->assertFalse($project->getRepositoryUrlAttribute());
        $this->assertFalse($project->repository_url);
    }

    /**
     * @dataProvider provideBranchUrl
     * @covers ::getBranchUrlAttribute
     */
    public function testGetBranchUrlAttribute($repository, $branch, $expected)
    {
        $project             = new Project();
        $project->branch     = $branch;
        $project->repository = $repository;

        $this->assertSame($expected, $project->getBranchUrlAttribute());
        $this->assertSame($expected, $project->branch_url);
    }

    /**
     * @dataProvider provideMalformedRepositoryUrl
     * @covers ::GetBranchUrlAttribute
     */
    public function testGetBranchUrlAttributeHandlesUnknown($repository)
    {
        $project             = new Project();
        $project->branch     = 'master';
        $project->repository = $repository;

        $this->assertFalse($project->getBranchUrlAttribute());
        $this->assertFalse($project->branch_url);
    }

    /**
     * @dataProvider provideBranchUrl
     * @covers ::getBranchUrlAttribute
     */
    public function testGetBranchUrlAttributeHandlesAlternativeBranch($repository, $branch, $expected)
    {
        $project             = new Project();
        $project->branch     = 'a-non-existent-branch-blah';
        $project->repository = $repository;

        $this->assertSame($expected, $project->getBranchUrlAttribute($branch));
    }

    public function provideBranchUrl()
    {
        return $this->fixture('Project')['branch_url'];
    }

    /**
     * @covers ::generateSSHKey
     */
    public function testGenerateSSHKey()
    {
        $expectedPrivateKey = 'a-private-key';
        $expectedPublicKey  = 'a-public-key';
        $id                 = 12345;
        $path               = storage_path('app/tmp/sshkey' . $id);

        File::shouldReceive('get')->once()->with($path)->andReturn($expectedPrivateKey);
        File::shouldReceive('get')->once()->with($path . '.pub')->andReturn($expectedPublicKey);
        File::shouldReceive('delete')->once()->with([$path, $path . '.pub']);

        $process = m::mock(Process::class);
        $process->shouldReceive('setScript')->with('tools.GenerateSSHKey', ['key_file' => $path])->andReturnSelf();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->andReturn(true);

        App::instance(Process::class, $process);

        $project     = new StubProject();
        $project->id = $id;
        $project->generateSSHKey();

        $this->assertSame($expectedPrivateKey, $project->private_key);
        $this->assertSame($expectedPublicKey, $project->public_key);
    }

    /**
     * @covers ::generateSSHKey
     */
    public function testGenerateSSHKeyShouldThrowExceptionOnFailure()
    {
        $this->expectException(RuntimeException::class);

        $id   = 12345;
        $path = storage_path('app/tmp/sshkey' . $id);

        /** @var Process $process */
        $process = m::mock(Process::class);
        $process->shouldReceive('setScript')->with('tools.GenerateSSHKey', ['key_file' => $path])->andReturnSelf();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->andReturn(false);
        $process->shouldReceive('getErrorOutput');

        App::instance(Process::class, $process);

        $project     = new StubProject();
        $project->id = $id;
        $project->generateSSHKey();
    }

    /**
     * @covers ::regeneratePublicKey
     */
    public function testRegeneratePublicKey()
    {
        $expectedPublicKey  = 'a-public-key';
        $expectedPrivateKey = 'a-private-key';
        $id                 = 12345;
        $path               = storage_path('app/tmp/sshkey' . $id);

        File::shouldReceive('put')->once()->with($path, $expectedPrivateKey);
        File::shouldReceive('chmod')->with($path, 0600);
        File::shouldReceive('get')->once()->with($path . '.pub')->andReturn($expectedPublicKey);
        File::shouldReceive('delete')->once()->with([$path, $path . '.pub']);

        /** @var Process $process */
        $process = m::mock(Process::class);
        $process->shouldReceive('setScript')
                ->with('tools.RegeneratePublicSSHKey', ['key_file' => $path])
                ->andReturnSelf();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->andReturn(true);

        App::instance(Process::class, $process);

        $project              = new StubProject();
        $project->private_key = $expectedPrivateKey;
        $project->id          = $id;
        $project->regeneratePublicKey();

        $this->assertSame($expectedPrivateKey, $project->private_key);
        $this->assertSame($expectedPublicKey, $project->public_key);
    }

    /**
     * @covers ::regeneratePublicKey
     */
    public function testRegeneratePublicKeyShouldThrowExceptionOnFailure()
    {
        $this->expectException(RuntimeException::class);

        $expectedPrivateKey = 'a-private-key';
        $id                 = 12345;
        $path               = storage_path('app/tmp/sshkey' . $id);

        File::shouldReceive('put')->once()->with($path, $expectedPrivateKey);
        File::shouldReceive('chmod')->with($path, 0600);

        /** @var Runner $process */
        $process = m::mock(Process::class);
        $process->shouldReceive('setScript')
                ->with('tools.RegeneratePublicSSHKey', ['key_file' => $path])
                ->andReturnSelf();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->andReturn(false);
        $process->shouldReceive('getErrorOutput');

        App::instance(Process::class, $process);

        $project              = new StubProject();
        $project->private_key = $expectedPrivateKey;
        $project->id          = $id;
        $project->regeneratePublicKey();
    }

    /**
     * @covers ::group
     */
    public function testGroup()
    {
        $project = new Project();
        $actual  = $project->group();

        $this->assertInstanceOf(BelongsTo::class, $actual);
        $this->assertBelongsTo('group', Project::class);
    }

    /**
     * @covers ::checkUrls
     */
    public function testCheckUrls()
    {
        $project  = new Project();
        $actual   = $project->checkUrls();

        $this->assertInstanceOf(HasMany::class, $actual);
        $this->assertHasMany('checkUrls', Project::class);
    }

    /**
     * @covers ::refs
     */
    public function testRefs()
    {
        $project  = new Project();
        $actual   = $project->refs();

        $this->assertInstanceOf(HasMany::class, $actual);
        $this->assertHasMany('refs', Project::class);
    }

    /**
     * @covers ::channels
     */
    public function testChannels()
    {
        $project  = new Project();
        $actual   = $project->channels();

        // TODO: Check the order by?
        $this->assertInstanceOf(HasMany::class, $actual);
        $this->assertHasMany('channels', Project::class);
    }

    /**
     * @covers ::deployments
     */
    public function testDeployments()
    {
        $project  = new Project();
        $actual   = $project->deployments();

        // TODO: Check the order by?
        $this->assertInstanceOf(HasMany::class, $actual);
        $this->assertHasMany('deployments', Project::class);
    }

    /**
     * @covers ::heartbeats
     */
    public function testHeartbeats()
    {
        $project  = new Project();
        $actual   = $project->heartbeats();

        // TODO: Check the order by?
        $this->assertInstanceOf(HasMany::class, $actual);
        $this->assertHasMany('heartbeats', Project::class);
    }

    /**
     * @covers ::servers
     */
    public function testServers()
    {
        $project  = new Project();
        $actual   = $project->servers();

        // TODO: Check the order by?
        $this->assertInstanceOf(HasMany::class, $actual);
        $this->assertHasMany('servers', Project::class);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\ProductRelations
     */
    public function testHasProjectRelations()
    {
        $this->assertHasProjectRelations(Project::class);
    }
}
