<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Routing\UrlGenerator;
use McCool\LaravelAutoPresenter\HasPresenter;
use Mockery as m;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\ProductRelations;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;
use REBELinBLUE\Deployer\View\Presenters\ProjectPresenter;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Project
 */
class ProjectTest extends TestCase
{
    use TestsModel, ProductRelations;

    /**
     * @covers ::__construct
     */
    public function testIsPresentable()
    {
        $project = new Project();

        $this->assertInstanceOf(HasPresenter::class, $project);
    }

    /**
     * @covers ::getPresenterClass
     */
    public function testGetPresenterClass()
    {
        $project   = new Project();
        $presenter = $project->getPresenterClass();

        $this->assertSame(ProjectPresenter::class, $presenter);
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

        $this->app->instance('url', $mock);

        $this->mockTokenGenerator($hash);

        $project = new Project();
        $project->generateHash();

        $this->assertSame($expected, $project->webhook_url);
        $this->assertSame($expected, $project->getWebhookUrlAttribute());
    }

    /**
     * @dataProvider provideStatuses
     *
     * @param int  $status
     * @param bool $expected
     */
    public function testIsDeploying(int $status, bool $expected)
    {
        $project         = new Project();
        $project->status = $status;

        $this->assertSame($expected, $project->isDeploying());
    }

    public function provideStatuses(): array
    {
        return $this->fixture('Project')['running'];
    }

    /**
     * @dataProvider provideRepositoryUrls
     * @covers ::mirrorPath
     *
     * @param string $repository
     * @param string $expected
     */
    public function testMirrorPath(string $repository, string $expected)
    {
        $project             = new Project();
        $project->repository = $repository;

        $folder = storage_path('app/mirrors/');
        $actual = $project->mirrorPath();

        $this->assertSame($expected, str_replace($folder, '', $actual));
        $this->assertStringStartsWith($folder, $actual);
    }

    public function provideRepositoryUrls(): array
    {
        return $this->fixture('Project')['repository_mirror_folder'];
    }

    /**
     * @dataProvider provideAccessDetails
     * @covers ::accessDetails
     *
     * @param string      $repository
     * @param string      $scheme
     * @param string|null $user
     * @param string      $domain
     * @param int|null    $port
     * @param string      $reference
     */
    public function testAccessDetails(
        string $repository,
        string $scheme,
        ?string $user,
        string $domain,
        ?int $port,
        string $reference
    ) {
        $project             = new Project();
        $project->repository = $repository;

        $actual = $project->accessDetails();

        $this->assertIsArray($actual);
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

    public function provideAccessDetails(): array
    {
        return $this->fixture('Project')['access_details'];
    }

    /**
     * @dataProvider provideMalformedRepositoryUrl
     * @covers ::accessDetails
     *
     * @param string $repository
     */
    public function testAccessDetailHandlesUnknown(string $repository)
    {
        $project             = new Project();
        $project->repository = $repository;

        $actual = $project->accessDetails();

        $this->assertIsArray($actual);
        $this->assertCount(0, $actual);
    }

    public function provideMalformedRepositoryUrl(): array
    {
        return array_chunk($this->fixture('Project')['malformed_url'], 1);
    }

    /**
     * @dataProvider provideRepositoryPath
     * @covers ::getRepositoryPathAttribute
     *
     * @param string $repository
     * @param string $expected
     */
    public function testGetRepositoryPathAttribute(string $repository, string $expected)
    {
        $project             = new Project();
        $project->repository = $repository;

        $this->assertSame($expected, $project->getRepositoryPathAttribute());
        $this->assertSame($expected, $project->repository_path);
    }

    public function provideRepositoryPath(): array
    {
        return $this->fixture('Project')['paths'];
    }

    /**
     * @dataProvider provideMalformedRepositoryUrl
     * @covers ::getRepositoryPathAttribute
     *
     * @param string $repository
     */
    public function testGetRepositoryPathAttributeHandlesUnknown(string $repository)
    {
        $project             = new Project();
        $project->repository = $repository;

        $this->assertFalse($project->getRepositoryPathAttribute());
        $this->assertFalse($project->repository_path);
    }

    /**
     * @dataProvider provideRepositoryUrl
     * @covers ::getRepositoryUrlAttribute
     *
     * @param string $repository
     * @param string $expected
     */
    public function testRepositoryUrlAttribute(string $repository, string $expected)
    {
        $project             = new Project();
        $project->repository = $repository;

        $this->assertSame($expected, $project->getRepositoryUrlAttribute());
        $this->assertSame($expected, $project->repository_url);
    }

    public function provideRepositoryUrl(): array
    {
        return $this->fixture('Project')['repo_url_to_web_url'];
    }

    /**
     * @dataProvider provideMalformedRepositoryUrl
     * @covers ::getRepositoryUrlAttribute
     *
     * @param string $repository
     */
    public function testRepositoryUrlAttributeHandlesUnknown(string $repository)
    {
        $project             = new Project();
        $project->repository = $repository;

        $this->assertFalse($project->getRepositoryUrlAttribute());
        $this->assertFalse($project->repository_url);
    }

    /**
     * @dataProvider provideBranchUrl
     * @covers ::getBranchUrlAttribute
     *
     * @param string $repository
     * @param string $branch
     * @param string $expected
     */
    public function testGetBranchUrlAttribute(string $repository, string $branch, string $expected)
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
     *
     * @param string $repository
     */
    public function testGetBranchUrlAttributeHandlesUnknown(string $repository)
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
     *
     * @param string $repository
     * @param string $branch
     * @param string $expected
     */
    public function testGetBranchUrlAttributeHandlesAlternativeBranch(
        string $repository,
        string $branch,
        string $expected
    ) {
        $project             = new Project();
        $project->branch     = 'a-non-existent-branch-blah';
        $project->repository = $repository;

        $this->assertSame($expected, $project->getBranchUrlAttribute($branch));
    }

    public function provideBranchUrl(): array
    {
        return $this->fixture('Project')['branch_url'];
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
     * @covers \REBELinBLUE\Deployer\Traits\ProjectRelations
     */
    public function testHasProjectRelations()
    {
        $this->assertHasProjectRelations(Project::class);
    }
}
