<?php

namespace REBELinBLUE\Deployer\Tests\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery as m;
use REBELinBLUE\Deployer\Jobs\UpdateGitMirror;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentProjectRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

class EloquentProjectRepositoryTest extends TestCase
{
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Project::class);
        $repository = new EloquentProjectRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function testImplementsProjectRepositoryInterfaceInterface()
    {
        $model      = m::mock(Project::class);
        $repository = new EloquentProjectRepository($model);

        $this->assertInstanceOf(ProjectRepositoryInterface::class, $repository);
    }

    public function testGetAll()
    {
        $expected = m::mock(Project::class);
        $expected->shouldReceive('get')->andReturnSelf();

        $model  = m::mock(Project::class);
        $model->shouldReceive('orderBy')->with('name')->andReturn($expected);

        $repository = new EloquentProjectRepository($model);
        $actual     = $repository->getAll();

        $this->assertEquals($expected, $actual);
    }

    public function testGetByHash()
    {
        $hash = 'a-project-hash';

        $expected = m::mock(Project::class);
        $expected->shouldReceive('firstOrFail')->andReturnSelf();

        $model = m::mock(Project::class);
        $model->shouldReceive('where')->with('hash', $hash)->andReturn($expected);

        $repository = new EloquentProjectRepository($model);
        $actual     = $repository->getByHash($hash);

        $this->assertEquals($expected, $actual);
    }

    public function testGetByHashShouldThrowModelNotFoundException()
    {
        $hash = 'a-Project-hash';

        $this->expectException(ModelNotFoundException::class);

        $expected = m::mock(Project::class);
        $expected->shouldReceive('firstOrFail')->andThrow(ModelNotFoundException::class);

        $model = m::mock(Project::class);
        $model->shouldReceive('where')->with('hash', $hash)->andReturn($expected);

        $repository = new EloquentProjectRepository($model);
        $repository->getByHash($hash);
    }

    public function testRefreshBranches()
    {
        $id = 1;

        $this->expectsJobs(UpdateGitMirror::class);

        $model = m::mock(Project::class);
        $model->shouldReceive('findOrFail')->with($id)->andReturnSelf();

        $repository = new EloquentProjectRepository($model);
        $repository->refreshBranches($id);
    }


    public function testRefreshBranchesThrowsModelNotFoundException()
    {
        $id = 1;
        $this->doesntExpectJobs(UpdateGitMirror::class);

        $this->expectException(ModelNotFoundException::class);

        $model = m::mock(Project::class);
        $model->shouldReceive('findOrFail')->with($id)->andThrow(ModelNotFoundException::class);

        $repository = new EloquentProjectRepository($model);
        $repository->refreshBranches($id);
    }
}
