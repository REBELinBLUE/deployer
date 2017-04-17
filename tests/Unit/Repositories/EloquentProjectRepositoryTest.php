<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery as m;
use REBELinBLUE\Deployer\Jobs\QueueUpdateGitMirror;
use REBELinBLUE\Deployer\Jobs\SetupProject;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentProjectRepository;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentProjectRepository
 */
class EloquentProjectRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(Project::class, EloquentProjectRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsProjectRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            Project::class,
            EloquentProjectRepository::class,
            ProjectRepositoryInterface::class
        );
    }

    /**
     * @covers ::getAll
     */
    public function testGetAll()
    {
        $expected = m::mock(Project::class);
        $expected->shouldReceive('get')->andReturnSelf();

        $model  = m::mock(Project::class);
        $model->shouldReceive('orderBy')->once()->with('name')->andReturn($expected);

        $repository = new EloquentProjectRepository($model);
        $actual     = $repository->getAll();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getByHash
     */
    public function testGetByHash()
    {
        $hash = 'a-project-hash';

        $expected = m::mock(Project::class);
        $expected->shouldReceive('firstOrFail')->andReturnSelf();

        $model = m::mock(Project::class);
        $model->shouldReceive('where')->once()->with('hash', $hash)->andReturn($expected);

        $repository = new EloquentProjectRepository($model);
        $actual     = $repository->getByHash($hash);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getByHash
     */
    public function testGetByHashShouldThrowModelNotFoundException()
    {
        $hash = 'a-Project-hash';

        $this->expectException(ModelNotFoundException::class);

        $expected = m::mock(Project::class);
        $expected->shouldReceive('firstOrFail')->andThrow(ModelNotFoundException::class);

        $model = m::mock(Project::class);
        $model->shouldReceive('where')->once()->with('hash', $hash)->andReturn($expected);

        $repository = new EloquentProjectRepository($model);
        $repository->getByHash($hash);
    }

    /**
     * @covers ::refreshBranches
     */
    public function testRefreshBranches()
    {
        $model_id = 1;

        $this->expectsJobs(QueueUpdateGitMirror::class);

        $model = m::mock(Project::class);
        $model->shouldReceive('findOrFail')->once()->with($model_id)->andReturnSelf();

        $repository = new EloquentProjectRepository($model);
        $repository->refreshBranches($model_id);
    }

    /**
     * @covers ::refreshBranches
     */
    public function testRefreshBranchesThrowsModelNotFoundException()
    {
        $model_id = 1;
        $this->doesntExpectJobs(QueueUpdateGitMirror::class);

        $this->expectException(ModelNotFoundException::class);

        $model = m::mock(Project::class);
        $model->shouldReceive('findOrFail')->once()->with($model_id)->andThrow(ModelNotFoundException::class);

        $repository = new EloquentProjectRepository($model);
        $repository->refreshBranches($model_id);
    }

    /**
     * @covers ::updateById
     */
    public function testUpdateByIdRemovesBlankPrivateKey()
    {
        $model_id     = 1;
        $fields       = ['foo' => 'bar', 'private_key' => ''];
        $update       = ['foo' => 'bar']; // This is what is expected to be passed to update

        $expected = m::mock(Project::class);
        $expected->shouldReceive('update')->once()->with($update);

        $model = m::mock(Project::class);
        $model->shouldReceive('findOrFail')->once()->with($model_id)->andReturn($expected);

        $repository = new EloquentProjectRepository($model);
        $actual     = $repository->updateById($fields, $model_id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::updateById
     */
    public function testUpdateByIdClearPublicKeyWhenPrivateKeyIsProvided()
    {
        $model_id     = 1;
        $fields       = ['foo' => 'bar', 'private_key' => 'a-new-key'];

        $expected = m::mock(Project::class);
        $expected->shouldReceive('update')->once()->with($fields);
        $expected->shouldReceive('setAttribute')->once()->with('public_key', '');

        $model = m::mock(Project::class);
        $model->shouldReceive('findOrFail')->once()->with($model_id)->andReturn($expected);

        $repository = new EloquentProjectRepository($model);
        $actual     = $repository->updateById($fields, $model_id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $this->doesntExpectJobs(SetupProject::class);

        $expected = 'a-model';
        $fields   = ['foo' => 'bar'];

        $model = m::mock(Project::class);
        $model->shouldReceive('create')->once()->with($fields)->andReturn($expected);

        $repository = new EloquentProjectRepository($model);
        $actual     = $repository->create($fields);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::create
     */
    public function testCreateWithTemplate()
    {
        $this->expectsJobs(SetupProject::class);

        $expected = m::mock(Project::class);

        $fields = ['foo' => 'bar', 'template_id' => 1];
        $create = ['foo' => 'bar']; // This is what is expected to be passed to create

        $model = m::mock(Project::class);
        $model->shouldReceive('create')->once()->with($create)->andReturn($expected);

        $repository = new EloquentProjectRepository($model);
        $actual     = $repository->create($fields);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::create
     */
    public function testCreateDoesNotSaveBlanKPrivateKey()
    {
        $expected = m::mock(Project::class);

        $fields = ['foo' => 'bar', 'private_key' => ''];
        $create = ['foo' => 'bar']; // This is what is expected to be passed to create

        $model = m::mock(Project::class);
        $model->shouldReceive('create')->once()->with($create)->andReturn($expected);

        $repository = new EloquentProjectRepository($model);
        $actual     = $repository->create($fields);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getLastMirroredBefore
     */
    public function testGetLastMirroredBefore()
    {
        $expected = m::mock(Project::class);

        $count    = 10;
        $last     = Carbon::create(2017, 2, 5, 16, 45, 00, 'UTC');
        $callback = function () {
            // Empty callback
        };

        $model = m::mock(Project::class);
        $model->shouldReceive('where')->once()->with('is_mirroring', false)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('last_mirrored', '<', $last)->andReturnSelf();
        $model->shouldReceive('orWhereNull')->once()->with('last_mirrored')->andReturnSelf();
        $model->shouldReceive('chunk')->once()->with($count, $callback)->andReturn($expected);

        $repository = new EloquentProjectRepository($model);
        $actual     = $repository->getLastMirroredBefore($last, $count, $callback);

        $this->assertSame($expected, $actual);
    }
}
