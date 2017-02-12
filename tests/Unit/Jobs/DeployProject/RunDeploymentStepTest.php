<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs\DeployProject;

use Illuminate\Cache\Repository as Cache;
use Illuminate\Support\Collection;
use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Jobs\DeployProject\LogFormatter;
use REBELinBLUE\Deployer\Jobs\DeployProject\RunDeploymentStep;
use REBELinBLUE\Deployer\Jobs\DeployProject\ScriptBuilder;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Tests\TestCase;

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

        $formatter = m::mock(LogFormatter::class);

        $filesystem = m::mock(Filesystem::class);

        $builder = m::mock(ScriptBuilder::class);
        $builder->shouldReceive('setup')->once()->with($deployment, $step, $release_archive, $private_key);

        $this->deployment      = $deployment;
        $this->step            = $step;
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
}
