<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Mockery as m;
use REBELinBLUE\Deployer\Jobs\UpdateGitMirror;
use REBELinBLUE\Deployer\Jobs\UpdateGitReferences;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Parser;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\UpdateGitMirror
 */
class UpdateGitMirrorTest extends TestCase
{
    /**
     * @var Process
     */
    private $process;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var Parser
     */
    private $parser;

    public function setUpExpections()
    {
        $key_file     = '/tmp/sshkey';
        $private_key  = 'a-private-key';
        $wrapper      = 'a-wrapper-script';
        $wrapper_file = '/tmp/gitwrapper';
        $mirror_path  = '/tmp/mirror.git';
        $repository   = 'git@git.example.com:repository/mirror.git';

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('private_key')->andReturn($private_key);
        $project->shouldReceive('getAttribute')->once()->with('repository')->andReturn($repository);
        $project->shouldReceive('mirrorPath')->once()->andReturn($mirror_path);
        $project->shouldReceive('setAttribute')->with('is_mirroring', true);
        $project->shouldReceive('setAttribute')->with('is_mirroring', false);
        $project->shouldReceive('save')->twice();

        $filesystem = m::mock(Filesystem::class);
        $filesystem->shouldReceive('tempnam')->once()->with(storage_path('app/tmp/'), 'key')->andReturn($key_file);
        $filesystem->shouldReceive('put')->once()->with($key_file, $private_key);
        $filesystem->shouldReceive('chmod')->once()->with($key_file, 0600);
        $filesystem->shouldReceive('tempnam')->once()->with(storage_path('app/tmp/'), 'ssh')->andReturn($wrapper_file);
        $filesystem->shouldReceive('put')->once()->with($wrapper_file, $wrapper);
        $filesystem->shouldReceive('chmod')->once()->with($wrapper_file, 0755);
        $filesystem->shouldReceive('delete')->once()->with([$wrapper_file, $key_file]);

        $parser = m::mock(Parser::class);
        $parser->shouldReceive('parseFile')->once()->with('tools.SSHWrapperScript', [
            'private_key' => $key_file,
        ])->andReturn($wrapper);

        $process = m::mock(Process::class);
        $process->shouldReceive('setScript')->once()->with('tools.MirrorGitRepository', [
            'wrapper_file' => $wrapper_file,
            'mirror_path'  => $mirror_path,
            'repository'   => $repository,
        ])->andReturnSelf();

        $this->project     = $project;
        $this->parser      = $parser;
        $this->process     = $process;
        $this->filesystem  = $filesystem;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleSuccessful()
    {
        $this->setUpExpections();

        $timestamp = '2017-01-01 12:00:00';

        $this->project->shouldReceive('freshTimestamp')->once()->andReturn($timestamp);
        $this->process->shouldReceive('run')->once();
        $this->process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $this->project->shouldReceive('setAttribute')->once()->with('last_mirrored', $timestamp);

        $this->expectsJobs(UpdateGitReferences::class);

        $job = new UpdateGitMirror($this->project);
        $job->handle($this->process, $this->parser, $this->filesystem);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleUnsuccessful()
    {
        $this->setUpExpections();

        $this->process->shouldReceive('run')->once();
        $this->process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $this->process->shouldReceive('getErrorOutput')->once();
        $this->project->shouldNotReceive('setAttribute')->with('last_mirrored', m::any());

        $this->doesntExpectJobs(UpdateGitReferences::class);
        $this->expectException(RuntimeException::class);

        $job = new UpdateGitMirror($this->project);
        $job->handle($this->process, $this->parser, $this->filesystem);
    }

    /**
     * @covers ::__construct
     */
    public function testItHasUnlimitedTimeout()
    {
        $project = m::mock(Project::class);
        $job     = new UpdateGitMirror($project);

        $this->assertSame(0, $job->timeout);
    }
}
