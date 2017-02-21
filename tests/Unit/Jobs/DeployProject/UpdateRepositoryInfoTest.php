<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs\DeployProject;

use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\DeployProject\UpdateRepositoryInfo;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;
use RuntimeException;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\DeployProject\UpdateRepositoryInfo
 */
class UpdateRepositoryInfoTest extends TestCase
{
    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function setUp()
    {
        parent::setUp();

        $deployment_id = 12987;
        $path          = '/var/repositories/mirror.git';
        $commit        = Deployment::LOADING;
        $branch        = 'master'; // FIXME: Branch is not used if $commit is set so this isn't testing that

        $repository = m::mock(UserRepositoryInterface::class);

        $project = m::mock(Project::class);
        $project->shouldReceive('mirrorPath')->andReturn($path);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('project')->andReturn($project);
        $deployment->shouldReceive('getAttribute')->with('branch')->andReturn($branch);
        $deployment->shouldReceive('getAttribute')->with('commit')->andReturn($commit);
        $deployment->shouldReceive('getAttribute')->with('id')->andReturn($deployment_id);

        $process = m::mock(Process::class);
        $process->shouldReceive('setScript')->once()->with('tools.GetCommitDetails', [
            'deployment'    => $deployment_id,
            'mirror_path'   => $path,
            'git_reference' => $branch,
        ])->andReturnSelf();
        $process->shouldReceive('run')->once();

        $this->deployment  = $deployment;
        $this->process     = $process;
        $this->repository  = $repository;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleIsSuccessfulAndHasUser()
    {
        $this->mockRepoInfo();

        $this->deployment->shouldReceive('getAttribute')->with('user_id')->andReturn(1);
        $this->deployment->shouldReceive('getAttribute')->with('source')->andReturnNull();
        $this->repository->shouldNotReceive('findByEmail');
        $this->deployment->shouldNotReceive('setAttribute')->with('user_id', m::any());

        $this->deployment->shouldReceive('save')->once();

        $this->job();
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleIsSuccessfulAndHasSource()
    {
        $this->mockRepoInfo();

        $this->deployment->shouldReceive('getAttribute')->with('user_id')->andReturnNull();
        $this->deployment->shouldReceive('getAttribute')->with('source')->andReturn('Github');
        $this->repository->shouldNotReceive('findByEmail');
        $this->deployment->shouldNotReceive('setAttribute')->with('user_id', m::any());

        $this->deployment->shouldReceive('save')->once();

        $this->job();
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleIsUnsuccessful()
    {
        $this->expectException(RuntimeException::class);
        $this->process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $this->process->shouldReceive('getErrorOutput')->once();

        $this->job();
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleFindsUserAndSetsUserIfSourceAndUserIdIsEmpty()
    {
        $id    = 1231;
        $email = 'admin@example.com';

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn($id);

        $this->mockRepoInfo();

        $this->deployment->shouldReceive('setAttribute')->once()->with('user_id', $id);

        $this->deployment->shouldReceive('getAttribute')->with('user_id')->andReturnNull();
        $this->deployment->shouldReceive('getAttribute')->with('source')->andReturnNull();
        $this->deployment->shouldReceive('getAttribute')->with('committer_email')->andReturn($email);
        $this->repository->shouldReceive('findByEmail')->with($email)->andReturn($user);

        $this->deployment->shouldReceive('save')->once();

        $this->job();
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleFindsUserAndDoesNotSetUserIfSourceAndUserIdIsEmpty()
    {
        $id    = 1231;
        $email = 'admin@example.com';

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn($id);

        $this->mockRepoInfo();

        $this->deployment->shouldReceive('getAttribute')->with('user_id')->andReturnNull();
        $this->deployment->shouldReceive('getAttribute')->with('source')->andReturnNull();
        $this->deployment->shouldReceive('getAttribute')->with('committer_email')->andReturn($email);
        $this->repository->shouldReceive('findByEmail')->with($email)->andReturnNull();

        $this->deployment->shouldReceive('save')->once();

        $this->job();
    }

    private function mockRepoInfo()
    {
        $output = " a-git-hash   \x09  committer \x09    admin@example.com ";
        $this->process->shouldReceive('isSuccessful')->once()->andReturn(true);

        $this->process->shouldNotReceive('getErrorOutput');
        $this->process->shouldReceive('getOutput')->andReturn($output);

        $this->deployment->shouldReceive('setAttribute')->with('commit', 'a-git-hash');
        $this->deployment->shouldReceive('setAttribute')->with('committer', 'committer');
        $this->deployment->shouldReceive('setAttribute')->with('committer_email', 'admin@example.com');
    }

    private function job()
    {
        $job = new UpdateRepositoryInfo($this->deployment);
        $job->handle($this->process, $this->repository);
    }
}
