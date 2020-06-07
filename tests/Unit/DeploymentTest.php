<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use McCool\LaravelAutoPresenter\HasPresenter;
use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;
use REBELinBLUE\Deployer\View\Presenters\DeploymentPresenter;
use REBELinBLUE\Deployer\View\Presenters\RuntimeInterface;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Deployment
 */
class DeploymentTest extends TestCase
{
    use TestsModel;

    /**
     * @covers ::__construct
     */
    public function testRuntimeInterfaceIsUsed()
    {
        $deployment = new Deployment();

        $this->assertInstanceOf(RuntimeInterface::class, $deployment);
    }

    /**
     * @covers ::project
     */
    public function testProject()
    {
        $deployment = new Deployment();
        $actual     = $deployment->project();

        $this->assertInstanceOf(BelongsTo::class, $actual);
        $this->assertBelongsTo('project', Deployment::class);
    }

    /**
     * @covers ::__construct
     */
    public function testIsPresentable()
    {
        $deployment = new Deployment();

        $this->assertInstanceOf(HasPresenter::class, $deployment);
    }

    /**
     * @covers ::getPresenterClass
     */
    public function testGetPresenterClass()
    {
        $deployment       = new Deployment();
        $presenter        = $deployment->getPresenterClass();

        $this->assertSame(DeploymentPresenter::class, $presenter);
    }

    /**
     * @covers ::runtime
     */
    public function testGetRuntime()
    {
        $deployment = new Deployment();

        $deployment->status      = Deployment::COMPLETED;
        $deployment->started_at  = Carbon::create(2017, 1, 1, 12, 15, 35, 'UTC');
        $deployment->finished_at = Carbon::create(2017, 1, 1, 12, 15, 47, 'UTC');

        $this->assertSame(12, $deployment->runtime());
    }

    /**
     * @covers ::runtime
     */
    public function testGetRuntimeWhenUnfinished()
    {
        $deployment = new Deployment();

        $this->assertFalse($deployment->runtime());
    }

    /**
     * @dataProvider provideStatuses
     * @covers ::isRunning
     *
     * @param int $status
     */
    public function testIsRunning(int $status)
    {
        $deployment         = new Deployment();
        $deployment->status = $status;

        $actual = $deployment->isRunning();

        if ($status === Deployment::DEPLOYING) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * @dataProvider provideStatuses
     * @covers ::isSuccessful
     *
     * @param int $status
     */
    public function testIsSuccessful(int $status)
    {
        $deployment         = new Deployment();
        $deployment->status = $status;

        $actual = $deployment->isSuccessful();

        if ($status === Deployment::COMPLETED) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * @dataProvider provideStatuses
     * @covers ::isPending
     *
     * @param int $status
     */
    public function testIsPending(int $status)
    {
        $deployment         = new Deployment();
        $deployment->status = $status;

        $actual = $deployment->isPending();

        if ($status === Deployment::PENDING) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * @dataProvider provideStatuses
     * @covers ::isFailed
     *
     * @param $status
     */
    public function testIsFailed(int $status)
    {
        $deployment         = new Deployment();
        $deployment->status = $status;

        $actual = $deployment->isFailed();

        if ($status === Deployment::FAILED) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * @dataProvider provideStatuses
     * @covers ::isAborting
     *
     * @param int $status
     */
    public function testIsAborting(int $status)
    {
        $deployment         = new Deployment();
        $deployment->status = $status;

        $actual = $deployment->isAborting();

        if ($status === Deployment::ABORTING) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * @dataProvider provideStatuses
     * @covers ::isAborted
     *
     * @param int $status
     */
    public function testIsAborted(int $status)
    {
        $deployment         = new Deployment();
        $deployment->status = $status;

        $actual = $deployment->isAborted();

        if ($status === Deployment::ABORTED) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    public function provideStatuses(): array
    {
        return array_chunk($this->fixture('Deployment')['statuses'], 1);
    }

    /**
     * @dataProvider provideStatuses
     * @covers ::getRepoFailureAttribute
     *
     * @param int $status
     */
    public function testGetRepoFailureAttribute(int $status)
    {
        $deployment         = new Deployment();
        $deployment->commit = Deployment::LOADING;
        $deployment->status = $status;

        if ($status === Deployment::FAILED) {
            $this->assertTrue($deployment->getRepoFailureAttribute());
            $this->assertTrue($deployment->repo_failure);
        } else {
            $this->assertFalse($deployment->getRepoFailureAttribute());
            $this->assertFalse($deployment->repo_failure);
        }
    }

    /**
     * @dataProvider provideStatuses
     * @covers ::getRepoFailureAttribute
     *
     * @param int $status
     */
    public function testGetRepoFailureAttributeIsAlwaysFalseWhenCommitPopulated(int $status)
    {
        $deployment         = new Deployment();
        $deployment->commit = 'a-git-commit-hash';
        $deployment->status = $status;

        $this->assertFalse($deployment->getRepoFailureAttribute());
        $this->assertFalse($deployment->repo_failure);
    }

    /**
     * @covers ::getBranchUrlAttribute
     */
    public function testGetBranchUrlAttribute()
    {
        $branch   = 'master';
        $expected = 'http://git.example.com/branch/master';

        $project = m::mock(Project::class);
        $project->shouldReceive('getBranchUrlAttribute')->with($branch)->andReturn($expected);

        $deployment          = new Deployment();
        $deployment->branch  = $branch;
        $deployment->setRelation('project', $project);

        $this->assertSame($expected, $deployment->getBranchUrlAttribute());
        $this->assertSame($expected, $deployment->branch_url);
    }

    /**
     * @dataProvider provideCommits
     * @covers ::getShortCommitAttribute
     *
     * @param string $commit
     * @param string $expected
     */
    public function testGetShortCommitAttribute(string $commit, string $expected)
    {
        $deployment         = new Deployment();
        $deployment->commit = $commit;

        $this->assertSame($expected, $deployment->getShortCommitAttribute());
        $this->assertSame($expected, $deployment->short_commit);
    }

    public function provideCommits(): array
    {
        return $this->fixture('Deployment')['short_commits'];
    }

    /**
     * @covers ::getCommitUrlAttribute
     */
    public function testGetCommtUrlAttributeIsEmptyWhenStillLoading()
    {
        $deployment         = new Deployment();
        $deployment->commit = Deployment::LOADING;

        $this->assertEmpty($deployment->getCommitUrlAttribute());
        $this->assertEmpty($deployment->commit_url);
    }

    /**
     * @dataProvider provideEmptyAccessDetails
     * @covers ::getCommitUrlAttribute
     *
     * @param array $expected
     */
    public function testGetCommitUrlAttributeIsEmptyWhenAccessDetailsAreUnknown(array $expected)
    {
        $project = m::mock(Project::class);
        $project->shouldReceive('accessDetails')->andReturn($expected);

        $deployment          = new Deployment();
        $deployment->commit  = 'a-git-commit-hash';
        $deployment->setRelation('project', $project);

        $this->assertEmpty($deployment->getCommitUrlAttribute());
        $this->assertEmpty($deployment->commit_url);
    }

    public function provideEmptyAccessDetails(): array
    {
        return $this->fixture('Deployment')['empty_access_details'];
    }

    /**
     * @dataProvider provideAccessDetails
     * @covers ::getCommitUrlAttribute
     *
     * @param array  $details
     * @param string $commit
     * @param string $expected
     */
    public function testGetCommitUrlAttribute(array $details, string $commit, string $expected)
    {
        $project = m::mock(Project::class);
        $project->shouldReceive('accessDetails')->andReturn($details);

        $deployment          = new Deployment();
        $deployment->commit  = $commit;
        $deployment->setRelation('project', $project);

        $this->assertSame($expected, $deployment->getCommitUrlAttribute());
        $this->assertSame($expected, $deployment->commit_url);
    }

    public function provideAccessDetails(): array
    {
        return $this->fixture('Deployment')['commit_urls'];
    }
}
