<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\AbortDeployment;
use REBELinBLUE\Deployer\Jobs\QueueDeployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentDeploymentRepository;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentDeploymentRepository
 */
class EloquentDeploymentRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(Deployment::class, EloquentDeploymentRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsServerRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            Deployment::class,
            EloquentDeploymentRepository::class,
            DeploymentRepositoryInterface::class
        );
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $fields   = ['foo' => 'bar'];

        $this->expectsJobs(QueueDeployment::class);

        $project  = m::mock(Project::class);

        $expected = m::mock(Deployment::class);
        $expected->shouldReceive('getAttribute')->once()->with('project')->andReturn($project);

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
        $expected_id = 1;

        $expected = m::mock(Deployment::class);
        $expected->shouldReceive('isAborting')->once()->andReturn(false);
        $expected->shouldReceive('setAttribute')->once()->with('status', Deployment::ABORTING);
        $expected->shouldReceive('save')->once()->andReturnSelf();

        $this->expectsJobs(AbortDeployment::class);

        $model = m::mock(Deployment::class);
        $model->shouldReceive('findOrFail')->once()->with($expected_id)->andReturn($expected);

        $repository = new EloquentDeploymentRepository($model);
        $repository->abort($expected_id);
    }

    /**
     * @covers ::abort
     */
    public function testAbortDoesNotAbortWhilstAlreadyAborting()
    {
        $expected_id = 1;

        $expected = m::mock(Deployment::class);
        $expected->shouldReceive('isAborting')->once()->andReturn(true);

        $this->doesntExpectJobs(AbortDeployment::class);

        $model = m::mock(Deployment::class);
        $model->shouldReceive('findOrFail')->once()->with($expected_id)->andReturn($expected);

        $repository = new EloquentDeploymentRepository($model);
        $repository->abort($expected_id);
    }

    /**
     * @covers ::abortQueued
     */
    public function testAbortQueued($is_webhook = false)
    {
        $project_id = 432;

        // 2 deployments
        $deployments = [
            $this->mockDeploymentToAbort($is_webhook),
            $this->mockDeploymentToAbort($is_webhook),
        ];

        $model = m::mock(Deployment::class);
        $model->shouldReceive('where')->once()->with('project_id', $project_id)->andReturnSelf();
        $model->shouldReceive('whereIn')
              ->once()
              ->with('status', [Deployment::DEPLOYING, Deployment::PENDING])
              ->andReturnSelf();
        $model->shouldReceive('orderBy')->once()->with('started_at', 'DESC')->andReturnSelf();
        $model->shouldReceive('get')->andReturn($deployments);

        $this->expectsJobs(AbortDeployment::class);

        $repository = new EloquentDeploymentRepository($model);
        $repository->abortQueued($project_id);
    }

    /**
     * @covers ::abortQueued
     */
    public function testAbortQueuedWebhookIsDeleted()
    {
        $this->testAbortQueued(true);
    }

    /**
     * @covers ::rollback
     */
    public function testRollback($reason = '')
    {
        $this->withoutJobs(); // Disable jobs as that is tested by create

        $expected = m::mock(Deployment::class);
        $expected->shouldReceive('getAttribute')->with('project')->andReturn(m::mock(Project::class));

        $expected_id = 1234;
        $committer   = 'bob';
        $email       = 'bob@example.com';
        $commit      = 'abcd1234efgh';
        $project_id  = 12;
        $branch      = 'master';

        $fields = [
            'committer'       => $committer,
            'committer_email' => $email,
            'commit'          => $commit,
            'project_id'      => $project_id,
            'branch'          => $branch,
            'reason'          => $reason,
        ];

        $previous = m::mock(Deployment::class);
        $previous->shouldReceive('getAttribute')->with('committer')->andReturn($committer);
        $previous->shouldReceive('getAttribute')->with('committer_email')->andReturn($email);
        $previous->shouldReceive('getAttribute')->with('commit')->andReturn($commit);
        $previous->shouldReceive('getAttribute')->with('project_id')->andReturn($project_id);
        $previous->shouldReceive('getAttribute')->with('branch')->andReturn($branch);

        $model = m::mock(Deployment::class);
        $model->shouldReceive('findOrFail')->once()->with($expected_id)->andReturn($previous);
        $model->shouldReceive('create')->once()->with($fields)->andReturn($expected);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->rollback($expected_id, $reason);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::rollback
     */
    public function testRollBackWithReason()
    {
        $this->testRollback('deployment reason');
    }

    /**
     * @covers ::rollback
     */
    public function testRollbackThrowsModelNotFoundException()
    {
        $expected_id = 1321;
        $this->expectException(ModelNotFoundException::class);

        $model = m::mock(Deployment::class);
        $model->shouldReceive('findOrFail')->once()->with($expected_id)->andThrow(ModelNotFoundException::class);

        $repository = new EloquentDeploymentRepository($model);
        $repository->rollback($expected_id);
    }

    /**
     * @covers ::getTodayCount
     * @covers ::getBetweenDates
     */
    public function testGetTodayCount()
    {
        $project_id       = 1;
        $count            = 10;

        $model = $this->mockDeploymentsBetweenDates($count, $project_id, true);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->getTodayCount($project_id);

        $this->assertSame($count, $actual);
    }

    /**
     * @covers ::getLastWeekCount
     * @covers ::getBetweenDates
     */
    public function testGetLastWeekCount()
    {
        $expected_id = 1;
        $expected    = 10;

        $model = $this->mockDeploymentsBetweenDates($expected, $expected_id);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->getLastWeekCount($expected_id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getPending
     * @covers ::getStatus
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
        $expected_id       = 1;
        $paginate          = 10;
        $expected          = m::mock(Deployment::class);
        $expected->shouldReceive('with')->once()->with('user', 'project')->andReturnSelf();
        $expected->shouldReceive('whereNotNull')->once()->with('started_at')->andReturnSelf();
        $expected->shouldReceive('orderBy')->once()->with('started_at', 'DESC')->andReturnSelf();
        $expected->shouldReceive('paginate')->once()->with($paginate)->andReturnSelf();

        $model = m::mock(Deployment::class);
        $model->shouldReceive('where')->once()->with('project_id', $expected_id)->andReturn($expected);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->getLatest($expected_id, $paginate);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getLatestSuccessful
     */
    public function testGetLatestSuccessful()
    {
        $expected_id       = 1;
        $expected          = 'a-deployment-model';

        $model = m::mock(Deployment::class);
        $model->shouldReceive('where')->once()->with('project_id', $expected_id)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('status', Deployment::COMPLETED)->andReturnSelf();
        $model->shouldReceive('whereNotNull')->once()->with('started_at')->andReturnSelf();
        $model->shouldReceive('orderBy')->once()->with('started_at', 'DESC')->andReturnSelf();
        $model->shouldReceive('first')->once()->andReturn($expected);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->getLatestSuccessful($expected_id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getTimeline
     */
    public function testGetTimeline()
    {
        $expected = m::mock(Deployment::class);
        $expected->shouldReceive('with')->once()->with('project')->andReturnSelf();
        $expected->shouldReceive('take')->once()->with(15)->andReturnSelf();
        $expected->shouldReceive('orderBy')->once()->with('started_at', 'DESC')->andReturnSelf();
        $expected->shouldReceive('get')->once()->andReturnSelf();

        $model = m::mock(Deployment::class);
        $model->shouldReceive('whereRaw')->once()->withAnyArgs()->andReturnSelf();
        $model->shouldReceive('whereNotNull')->once()->with('started_at')->andReturn($expected);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->getTimeline();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getRunning
     * @covers ::getStatus
     */
    public function testGetRunning()
    {
        $expected = m::mock(Deployment::class);

        $model = $this->mockDeploymentWithStatus(Deployment::DEPLOYING, $expected);

        $repository = new EloquentDeploymentRepository($model);
        $actual     = $repository->getRunning();

        $this->assertSame($expected, $actual);
    }

    private function mockDeploymentToAbort($is_webhook = false)
    {
        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('setAttribute')->once()->with('status', Deployment::ABORTING);
        $deployment->shouldReceive('save');

        $deployment->shouldReceive('getAttribute')->once()->with('is_webhook')->andReturn($is_webhook);

        if ($is_webhook) {
            $deployment->shouldReceive('delete');
        }

        return $deployment;
    }

    private function mockDeploymentsBetweenDates($count, $project_id, $sameDay = false)
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
        $model->shouldReceive('where')->once()->with('project_id', $project_id)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('started_at', '>=', $start)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('started_at', '<=', $end)->andReturnSelf();
        $model->shouldReceive('count')->once()->andReturn($count);

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
