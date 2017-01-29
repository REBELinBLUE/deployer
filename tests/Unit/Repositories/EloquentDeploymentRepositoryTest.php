<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Carbon\Carbon;
use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\AbortDeployment;
use REBELinBLUE\Deployer\Jobs\QueueDeployment;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentDeploymentRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentDeploymentRepository
 */
class EloquentDeploymentRepositoryTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Deployment::class);
        $repository = new EloquentDeploymentRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsServerRepositoryInterface()
    {
        $model      = m::mock(Deployment::class);
        $repository = new EloquentDeploymentRepository($model);

        $this->assertInstanceOf(DeploymentRepositoryInterface::class, $repository);
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $this->markTestSkipped('not working - same issue as ProjectRepository');

        $expected = m::mock(Deployment::class);

        $fields   = ['foo' => 'bar'];

        $this->expectsJobs(QueueDeployment::class);

        $model = m::mock(Deployment::class);
        $model->shouldReceive('create')->once()->with($fields)->andReturn($expected);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->create($fields);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::create
     */
    public function testCreateClearsOptionalCommands()
    {
        $this->markTestSkipped('not working - same issue as ProjectRepository');

        $project  = m::mock(Project::class);
        $expected = m::mock(Deployment::class);
        $expected->shouldReceive('getAttribute')->once()->with('project')->andReturn($project);

        $fields   = ['foo' => 'bar', 'optional' => ['an-optional-command']];
        $create   = ['foo' => 'bar'];

        $this->expectsJobs(QueueDeployment::class);

        $model = m::mock(Deployment::class);
        $model->shouldReceive('create')->once()->with($create)->andReturn($expected);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->create($fields);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::abort
     */
    public function testAbort()
    {
        $id = 1;

        $expected = m::mock(Deployment::class);
        $expected->shouldReceive('isAborting')->once()->andReturn(false);
        $expected->shouldReceive('setAttribute')->once()->with('status', Deployment::ABORTING);
        $expected->shouldReceive('save')->once()->andReturnSelf();

        $this->expectsJobs(AbortDeployment::class);

        $model = m::mock(Deployment::class);
        $model->shouldReceive('findOrFail')->once()->with($id)->andReturn($expected);

        $repository = new EloquentDeploymentRepository($model);
        $repository->abort($id);
    }

    /**
     * @covers ::abort
     */
    public function testAbortDoesNotAbortWhilstAlreadyAborting()
    {
        $id = 1;

        $expected = m::mock(Deployment::class);
        $expected->shouldReceive('isAborting')->once()->andReturn(true);

        $this->doesntExpectJobs(AbortDeployment::class);

        $model = m::mock(Deployment::class);
        $model->shouldReceive('findOrFail')->once()->with($id)->andReturn($expected);

        $repository = new EloquentDeploymentRepository($model);
        $repository->abort($id);
    }

    /**
     * @covers ::abortQueued
     */
    public function testAbortQueued()
    {
        $this->markTestIncomplete('not yet implemented');
    }

    /**
     * @covers ::rollback
     */
    public function testRollback()
    {
        $this->markTestIncomplete('not yet implemented');
    }

    /**
     * @covers ::getTodayCount
     */
    public function testGetTodayCount()
    {
        $id       = 1;
        $expected = 10;

        $model = $this->mockDeploymentsBetweenDates($expected, $id, true);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->getTodayCount($id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getLastWeekCount
     */
    public function testGetLastWeekCount()
    {
        $id       = 1;
        $expected = 10;

        $model = $this->mockDeploymentsBetweenDates($expected, $id);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->getLastWeekCount($id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getPending
     */
    public function testGetPending()
    {
        $expected = m::mock(Deployment::class);

        $model = $this->mockDeploymentWithStatus(Deployment::PENDING, $expected);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->getPending();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getLatest
     */
    public function testGetLatest()
    {
        $this->markTestSkipped('not working - same issue as ProjectRepository');

        $id       = 1;
        $paginate = 10;
        $expected = m::mock(Deployment::class);

        $model = m::mock(Deployment::class);
        $model->shouldReceive('where')->once()->with('project_id', $id)->andReturnSelf();
        $model->shouldReceive('with')->once()->with('user', 'project')->andReturnSelf();
        $model->shouldReceive('whereNotNull')->once()->with('started_at')->andReturnSelf();
        $model->shouldReceive('orderBy')->once()->with('started_at', 'DESC')->andReturnSelf();
        $model->shouldReceive('paginate')->once()->with($paginate)->andReturn($expected);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->getLatest($id, $paginate);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getLatestSuccessful
     */
    public function testGetLatestSuccessful()
    {
        $id       = 1;
        $expected = 'a-deployment-model';

        $model = m::mock(Deployment::class);
        $model->shouldReceive('where')->once()->with('project_id', $id)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('status', Deployment::COMPLETED)->andReturnSelf();
        $model->shouldReceive('whereNotNull')->once()->with('started_at')->andReturnSelf();
        $model->shouldReceive('orderBy')->once()->with('started_at', 'DESC')->andReturnSelf();
        $model->shouldReceive('first')->once()->andReturn($expected);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->getLatestSuccessful($id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getTimeline
     */
    public function testGetTimeline()
    {
        $this->markTestSkipped('not working - same issue as ProjectRepository');

        $expected = m::mock(Deployment::class);

        $model = m::mock(Deployment::class);
        $model->shouldReceive('whereRaw')->once()->withAnyArgs()->andReturnSelf();
        $model->shouldReceive('whereNotNull')->once()->with('started_at')->andReturnSelf();
        $model->shouldReceive('with')->once()->with('project')->andReturnSelf();
        $model->shouldReceive('take')->once()->with(15)->andReturnSelf();
        $model->shouldReceive('orderBy')->once()->with('started_at', 'DESC')->andReturnSelf();
        $model->shouldReceive('get')->once()->andReturn($expected);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->getTimeline();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getRunning
     */
    public function testGetRunning()
    {
        $expected = m::mock(Deployment::class);

        $model = $this->mockDeploymentWithStatus(Deployment::DEPLOYING, $expected);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->getRunning();

        $this->assertSame($expected, $actual);
    }

    private function mockDeploymentsBetweenDates($expected, $id, $sameDay = false)
    {
        Carbon::setTestNow(Carbon::create(2017, 1, 26, 0, 0, 0, 'UTC'));

        if ($sameDay) {
            $start = '2017-01-26 00:00:00';
            $end   = '2017-01-26 23:59:59';
        } else {
            $start = '2017-01-19 00:00:00';
            $end   = '2017-01-25 23:59:59';
        }

        $model = m::mock(Deployment::class);
        $model->shouldReceive('where')->once()->with('project_id', $id)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('started_at', '>=', $start)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('started_at', '<=', $end)->andReturnSelf();
        $model->shouldReceive('count')->once()->andReturn($expected);

        return $model;
    }

    private function mockDeploymentWithStatus($status, $expected)
    {
        $model = m::mock(Deployment::class);
        $model->shouldReceive('whereRaw')->once()->withAnyArgs()->andReturnSelf();
        $model->shouldReceive('where')->once()->with('status', $status)->andReturnSelf();
        $model->shouldReceive('whereNotNull')->once()->with('started_at')->andReturnSelf();
        $model->shouldReceive('orderBy')->once()->with('started_at', 'DESC')->andReturnSelf();
        $model->shouldReceive('get')->once()->andReturn($expected);

        return $model;
    }
}
