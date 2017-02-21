<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Mockery as m;
use REBELinBLUE\Deployer\Jobs\UpdateGitReferences;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\RefRepositoryInterface;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\UpdateGitReferences
 */
class UpdateGitReferencesTest extends TestCase
{
    /**
     * @var Process
     */
    private $process;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var int
     */
    private $project_id = 123456;

    /**
     * @var RefRepositoryInterface
     */
    private $repository;

    public function setUp()
    {
        parent::setUp();

        $mirror_dir = '/var/repositories/mirror.git';

        $process = m::mock(Process::class);
        $process->shouldReceive('setScript')->once()->with('tools.ListGitReferences', [
            'mirror_path'   => $mirror_dir,
            'git_reference' => 'tag',
        ])->andReturnSelf();

        $process->shouldReceive('setScript')->once()->with('tools.ListGitReferences', [
            'mirror_path'   => $mirror_dir,
            'git_reference' => 'branch',
        ])->andReturnSelf();

        $refs = m::mock(Ref::class); // FIXME: Is this the right class?
        $refs->shouldReceive('delete')->once();

        $project = m::mock(Project::class);
        $project->shouldReceive('mirrorPath')->once()->andReturn($mirror_dir);
        $project->shouldReceive('refs')->once()->andReturn($refs);
        $project->shouldReceive('getAttribute')->with('id')->andReturn($this->project_id);

        $repository = m::mock(RefRepositoryInterface::class);

        $this->process    = $process;
        $this->repository = $repository;
        $this->project    = $project;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle()
    {
        $tags     = ['1.0.0', '0.0.1', '    ', '0.0.2'];
        $branches = [' * master ', 'develop', 'release', 'bug/12345'];

        $this->process->shouldReceive('run')->twice();
        $this->process->shouldReceive('isSuccessful')->twice()->andReturn(true);
        $this->process->shouldReceive('getOutput')->once()->andReturn(implode(PHP_EOL, $tags));
        $this->process->shouldReceive('getOutput')->once()->andReturn(implode(PHP_EOL, $branches));

        foreach ($tags as $tag) {
            if ($tag === '    ') { // This is to ensure it is trimmed so and never passed to the repository
                continue;
            }

            $this->repository->shouldReceive('create')->once()->with([
                'name'       => $tag,
                'project_id' => $this->project_id,
                'is_tag'     => true,
            ]);
        }

        foreach ($branches as $branch) {
            if ($branch === ' * master ') { // Tests that it is trimmed, a leading * is removed and then trimmed again
                $branch = 'master';
            }

            $this->repository->shouldReceive('create')->once()->with([
                'name'       => $branch,
                'project_id' => $this->project_id,
                'is_tag'     => false,
            ]);
        }

        $job = new UpdateGitReferences($this->project);
        $job->handle($this->process, $this->repository);
    }

    /**
     * @covers:: __construct
     * @covers:: handle
     */
    public function testHandlesFailure()
    {
        $this->process->shouldReceive('run')->twice();
        $this->process->shouldReceive('isSuccessful')->twice()->andReturn(false);
        $this->process->shouldNotReceive('getOutput');
        $this->repository->shouldNotReceive('create');

        $job = new UpdateGitReferences($this->project);
        $job->handle($this->process, $this->repository);
    }

    // TODO: Should we refactor to handle the situation where just one fails? and what should we do in that case
}
