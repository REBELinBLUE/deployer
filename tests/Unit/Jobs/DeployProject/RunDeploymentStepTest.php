<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs\DeployProject;

use Carbon\Carbon;
use Closure;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Support\Collection;
use Mockery as m;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Exceptions\CancelledDeploymentException;
use REBELinBLUE\Deployer\Exceptions\FailedDeploymentException;
use REBELinBLUE\Deployer\Jobs\AbortDeployment;
use REBELinBLUE\Deployer\Jobs\DeployProject\LogFormatter;
use REBELinBLUE\Deployer\Jobs\DeployProject\RunDeploymentStep;
use REBELinBLUE\Deployer\Jobs\DeployProject\ScriptBuilder;
use REBELinBLUE\Deployer\Jobs\DeployProject\SendFileToServer;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Process\Process as SymfonyProcess;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\DeployProject\RunDeploymentStep
 */
class RunDeploymentStepTest extends TestCase
{
    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var DeployStep
     */
    private $step;

    /**
     * @var string
     */
    private $private_key;

    /**
     * @var string
     */
    private $release_archive;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var ScriptBuilder
     */
    private $builder;

    /**
     * @var LogFormatter
     */
    private $formatter;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $cache_key;

    /**
     * @var Server
     */
    private $server;

    public function setUp()
    {
        parent::setUp();

        $deployment_id   = 12392;
        $private_key     = '/tmp/id_rsa.key';
        $release_archive = '/tmp/release.tar.gz';

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('id')->andReturn($deployment_id);

        $step = m::mock(DeployStep::class);

        $cache = m::mock(Cache::class);

        $server = m::mock(Server::class);

        $formatter = m::mock(LogFormatter::class);

        $filesystem = m::mock(Filesystem::class);

        $builder = m::mock(ScriptBuilder::class);
        $builder->shouldReceive('setup')->once()->with($deployment, $step, $release_archive, $private_key);

        $this->cache_key       = AbortDeployment::CACHE_KEY_PREFIX . $deployment_id;
        $this->deployment      = $deployment;
        $this->step            = $step;
        $this->server          = $server;
        $this->cache           = $cache;
        $this->formatter       = $formatter;
        $this->filesystem      = $filesystem;
        $this->builder         = $builder;
        $this->private_key     = $private_key;
        $this->release_archive = $release_archive;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::run
     */
    public function testHandle()
    {
        $this->step->shouldReceive('getAttribute')->with('servers')->andReturn(new Collection());

        $job = new RunDeploymentStep($this->deployment, $this->step, $this->private_key, $this->release_archive);
        $job->handle($this->cache, $this->formatter, $this->filesystem, $this->builder);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::run
     * @covers ::sendFilesForStep
     * @covers ::runDeploymentStepOnServer
     */
    public function testRun()
    {
        $log = $this->mockLog();

        $log->shouldReceive('setAttribute')->with('status', ServerLog::COMPLETED);

        $this->cache->shouldReceive('pull')->with($this->cache_key)->andReturnNull();

        $this->step->shouldReceive('getAttribute')->with('stage')->andReturn(Command::BEFORE_INSTALL);

        $job = new RunDeploymentStep($this->deployment, $this->step, $this->private_key, $this->release_archive);
        $job->handle($this->cache, $this->formatter, $this->filesystem, $this->builder);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::run
     * @covers ::sendFilesForStep
     * @covers ::runDeploymentStepOnServer
     * @covers ::canBeCancelled
     */
    public function testRunWithCacheEntryThrowsCancelledDeploymentException()
    {
        $this->expectException(CancelledDeploymentException::class);

        $log = $this->mockLog();

        $log->shouldNotReceive('setAttribute')->with('status', ServerLog::COMPLETED);
        $log->shouldReceive('setAttribute')->with('status', ServerLog::CANCELLED);

        $this->cache->shouldReceive('pull')->with($this->cache_key)->andReturn(true);

        $this->step->shouldReceive('getAttribute')->with('stage')->andReturn(Command::BEFORE_INSTALL);

        $job = new RunDeploymentStep($this->deployment, $this->step, $this->private_key, $this->release_archive);
        $job->handle($this->cache, $this->formatter, $this->filesystem, $this->builder);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::run
     * @covers ::runDeploymentStepOnServer
     */
    public function testRunDeploymentStepOnServerThrowsFailedDeploymentExceptionOnFailure()
    {
        $this->expectException(FailedDeploymentException::class);

        $process = m::mock(Process::class);

        $process->shouldReceive('run')->once()->with(m::type('callable'));

        $process->shouldReceive('isSuccessful')->andReturn(false);
        $process->shouldReceive('getErrorOutput');

        $log = $this->mockLog($process);

        $log->shouldReceive('setAttribute')->with('output', '');
        $log->shouldReceive('setAttribute')->with('status', ServerLog::FAILED);

        $this->cache->shouldReceive('pull')->with($this->cache_key)->andReturnNull();

        $this->step->shouldReceive('getAttribute')->with('stage')->andReturn(Command::BEFORE_INSTALL);

        $job = new RunDeploymentStep($this->deployment, $this->step, $this->private_key, $this->release_archive);
        $job->handle($this->cache, $this->formatter, $this->filesystem, $this->builder);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::run
     * @covers ::runDeploymentStepOnServer
     * @todo refactor this is horrible!
     */
    public function testRunDeploymentStepOnServerWithOutput()
    {
        $this->expectException(FailedDeploymentException::class);

        $process = m::mock(Process::class);
        $process->shouldReceive('run')->once()->with(m::on(function ($callback) {
            $callback(SymfonyProcess::ERR, 'a-line-of-output' . PHP_EOL);
            $callback(SymfonyProcess::OUT, 'a-second-line');
            $this->assertInstanceOf(Closure::class, $callback);

            return true;
        }));

        $process->shouldReceive('isSuccessful')->andReturn(false);
        $process->shouldReceive('getErrorOutput');

        $log = $this->mockLog($process, 2);

        $this->formatter->shouldReceive('error')->with('a-line-of-output' . PHP_EOL)->andReturn('err-line' . PHP_EOL);
        $this->formatter->shouldReceive('info')->with('a-second-line')->andReturn('second-line');

        $log->shouldReceive('setAttribute')->with('output', 'err-line' . PHP_EOL);
        $log->shouldReceive('setAttribute')->with('output', 'err-line' . PHP_EOL . 'second-line');
        $log->shouldReceive('setAttribute')->with('status', ServerLog::FAILED);

        $this->cache->shouldReceive('has')->with($this->cache_key)->andReturn(false);
        $this->cache->shouldReceive('pull')->with($this->cache_key)->andReturnNull();

        $this->step->shouldReceive('getAttribute')->with('stage')->andReturn(Command::BEFORE_INSTALL);

        $job = new RunDeploymentStep($this->deployment, $this->step, $this->private_key, $this->release_archive);
        $job->handle($this->cache, $this->formatter, $this->filesystem, $this->builder);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::run
     * @covers ::runDeploymentStepOnServer
     */
    public function testRunDeploymentStepOnServerWithOutputCancelsDeployment()
    {
        $this->expectException(CancelledDeploymentException::class);

        $process = m::mock(Process::class);
        $process->shouldReceive('run')->once()->with(m::on(function ($callback) {
            $callback(SymfonyProcess::OUT, 'a-line');
            $this->assertInstanceOf(Closure::class, $callback);

            return true;
        }));

        $process->shouldReceive('stop')->once()->with(0, SIGINT);
        $process->shouldReceive('isSuccessful')->andReturn(false);

        $log = $this->mockLog($process, 1);

        $this->formatter->shouldReceive('info')->with('a-line')->andReturn('a-line');
        $this->formatter->shouldReceive('error')->with('SIGINT - Cancelled')->andReturn();

        $log->shouldReceive('setAttribute')->with('output', 'a-line');
        $log->shouldReceive('setAttribute')->with('status', ServerLog::CANCELLED);

        $this->cache->shouldReceive('has')->with($this->cache_key)->andReturn(true);
        $this->cache->shouldReceive('pull')->with($this->cache_key)->andReturn(true);

        $this->step->shouldReceive('getAttribute')->with('stage')->andReturn(Command::BEFORE_INSTALL);

        $job = new RunDeploymentStep($this->deployment, $this->step, $this->private_key, $this->release_archive);
        $job->handle($this->cache, $this->formatter, $this->filesystem, $this->builder);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::run
     * @covers ::sendFilesForStep
     * @covers ::runDeploymentStepOnServer
     * @covers ::canBeCancelled
     */
    public function testRunWithCacheEntryDoesNotThrowCancelledDeploymentExceptionWhenTooLate()
    {
        $log = $this->mockLog();

        $log->shouldReceive('setAttribute')->with('status', ServerLog::COMPLETED);
        $log->shouldNotReceive('setAttribute')->with('status', ServerLog::CANCELLED);

        $this->cache->shouldReceive('pull')->with($this->cache_key)->andReturn(true);

        $this->step->shouldReceive('getAttribute')->with('stage')->andReturn(Command::AFTER_ACTIVATE);

        $job = new RunDeploymentStep($this->deployment, $this->step, $this->private_key, $this->release_archive);
        $job->handle($this->cache, $this->formatter, $this->filesystem, $this->builder);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::run
     * @covers ::sendFilesForStep
     * @covers ::runDeploymentStepOnServer
     * @covers ::sendFile
     */
    public function testRunOnCloneStepUploadsArchive()
    {
        $path       = '/var/www/deployer';
        $release_id = 20171601161556;

        $log = $this->mockLog();

        $this->expectsJobs(SendFileToServer::class);

        $this->server->shouldReceive('getAttribute')->with('clean_path')->andReturn($path);
        $this->deployment->shouldReceive('getAttribute')->with('release_id')->andReturn($release_id);

        $log->shouldReceive('setAttribute')->with('status', ServerLog::COMPLETED);

        $this->cache->shouldReceive('pull')->with($this->cache_key)->andReturnNull();

        $this->step->shouldReceive('getAttribute')->with('stage')->andReturn(Command::DO_CLONE);

        $job = new RunDeploymentStep($this->deployment, $this->step, $this->private_key, $this->release_archive);
        $job->handle($this->cache, $this->formatter, $this->filesystem, $this->builder);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::run
     * @covers ::sendFilesForStep
     * @covers ::runDeploymentStepOnServer
     * @covers ::sendFileFromString
     * @covers ::sendFile
     */
    public function testRunOnInstallStepUploadsConfigFiles()
    {
        $path         = '/var/www/deployer';
        $config       = 'config.yml';
        $release_id   = 20171601161556;
        $content      = 'the-file-content';
        $tmp          = '/tmp/a-tmp-file-name';

        $log = $this->mockLog();

        $this->expectsJobs(SendFileToServer::class);

        $file = m::mock(ConfigFile::class);
        $file->shouldReceive('getAttribute')->with('path')->andReturn($config);
        $file->shouldReceive('getAttribute')->with('content')->andReturn($content);

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->with('configFiles')->andReturn(new Collection([$file]));

        $this->server->shouldReceive('getAttribute')->with('clean_path')->andReturn($path);
        $this->deployment->shouldReceive('getAttribute')->with('release_id')->andReturn($release_id);
        $this->deployment->shouldReceive('getAttribute')->with('project')->andReturn($project);

        $log->shouldReceive('setAttribute')->with('status', ServerLog::COMPLETED);

        $this->filesystem->shouldReceive('tempnam')->with(storage_path('app/tmp/'), 'tmp')->andReturn($tmp);
        $this->filesystem->shouldReceive('put')->with($tmp, $content);
        $this->filesystem->shouldReceive('delete')->with($tmp);

        $this->cache->shouldReceive('pull')->with($this->cache_key)->andReturnNull();

        $this->step->shouldReceive('getAttribute')->with('stage')->andReturn(Command::DO_INSTALL);

        $job = new RunDeploymentStep($this->deployment, $this->step, $this->private_key, $this->release_archive);
        $job->handle($this->cache, $this->formatter, $this->filesystem, $this->builder);
    }

    private function mockLog($process = null, $lines_of_output = 0)
    {
        $started_at  = Carbon::create(2017, 2, 1, 12, 45, 54, 'UTC');
        $finished_at = Carbon::create(2017, 2, 1, 12, 47, 12, 'UTC');

        $log = m::mock(ServerLog::class);
        $log->shouldReceive('setAttribute')->with('status', ServerLog::RUNNING);
        $log->shouldReceive('setAttribute')->with('started_at', $started_at);
        $log->shouldReceive('setAttribute')->with('finished_at', $finished_at);
        $log->shouldReceive('freshTimestamp')->once()->andReturn($started_at);
        $log->shouldReceive('freshTimestamp')->once()->andReturn($finished_at);
        $log->shouldReceive('getAttribute')->with('server')->andReturn($this->server);
        $log->shouldReceive('save')->times(2 + $lines_of_output);

        $this->step->shouldReceive('getAttribute')->with('servers')->andReturn(new Collection([$log]));

        $this->builder->shouldReceive('buildScript')->once()->with($this->server)->andReturn($process);

        return $log;
    }
}
