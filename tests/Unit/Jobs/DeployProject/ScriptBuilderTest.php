<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs\DeployProject;

use Exception;
use Illuminate\Support\Collection;
use Mockery as m;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Jobs\DeployProject\ScriptBuilder;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Services\Scripts\Parser as ScriptParser;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\DeployProject\ScriptBuilder
 */
class ScriptBuilderTest extends TestCase
{
    /**
     * @var Process
     */
    private $process;

    /**
     * @var ScriptParser
     */
    private $parser;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var DeployStep
     */
    private $step;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var string
     */
    private $release_archive;

    /**
     * @var string
     */
    private $private_key;

    public function setUp()
    {
        parent::setUp();

        $deployment_id   = 12312;
        $release_archive = 'release.tar.gz';
        $private_key     = '/tmp/id_rsa.private.key';
        $clean_path      = '/var/www';
        $release_id      = 20170110155645;
        $branch          = 'master';
        $commit          = 'e94168a2cb070d1b3163b58fb052285d3ea9ba12';
        $short_commit    = 'e94168a';
        $committer_email = 'committer@example.com';
        $committer       = 'committer-name';
        $user            = 'root';

        $parser = m::mock(ScriptParser::class);

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->with('variables')->andReturn(new Collection());
        $project->shouldReceive('getAttribute')->with('include_dev')->andReturn(true);
        $project->shouldReceive('getAttribute')->with('builds_to_keep')->andReturn(5);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('project')->andReturn($project);
        $deployment->shouldReceive('getAttribute')->with('release_id')->andReturn($release_id);
        $deployment->shouldReceive('getAttribute')->with('id')->andReturn($deployment_id);
        $deployment->shouldReceive('getAttribute')->with('branch')->andReturn($branch);
        $deployment->shouldReceive('getAttribute')->with('id')->andReturn($deployment_id);
        $deployment->shouldReceive('getAttribute')->with('commit')->andReturn($commit);
        $deployment->shouldReceive('getAttribute')->with('short_commit')->andReturn($short_commit);
        $deployment->shouldReceive('getAttribute')->with('committer_email')->andReturn($committer_email);
        $deployment->shouldReceive('getAttribute')->with('committer')->andReturn($committer);

        $command = m::mock(Command::class);
        $command->shouldReceive('getAttribute')->with('user')->andReturnNull();

        $step = m::mock(DeployStep::class);
        //$step->shouldReceive('getAttribute')->with('command')->andReturn($command);

        $server = m::mock(Server::class);
        $server->shouldReceive('getAttribute')->with('clean_path')->andReturn($clean_path);
        $server->shouldReceive('getAttribute')->with('user')->andReturn($user);

        $process = m::mock(Process::class);
        $process->shouldReceive('prependScript')->with('')->andReturnSelf();
        $process->shouldReceive('setServer')->with($server, $private_key, $user);

        $this->process         = $process;
        $this->parser          = $parser;
        $this->deployment      = $deployment;
        $this->server          = $server;
        $this->step            = $step;
        $this->release_archive = $release_archive;
        $this->private_key     = $private_key;
    }

    /**
     * @dataProvider provideDeploySteps
     * @covers ::__construct
     * @covers ::setup
     * @covers ::buildScript
     * @covers ::getScriptForStep
     */
    public function testBuildScriptForDeployStep($stage, $script)
    {
        // FIXME: - Line 115 & 135 not covered
        $this->step->shouldReceive('isCustom')->andReturn(false);
        $this->step->shouldReceive('getAttribute')->with('stage')->andReturn($stage);

        $this->process->shouldReceive('setScript')->with($script, m::type('array'))->andReturnSelf();

        $this->deployment->shouldReceive('getAttribute')->with('user')->andReturnNull();
        $this->deployment->shouldReceive('getAttribute')->with('is_webhook')->andReturn(true);
        $this->deployment->shouldReceive('getAttribute')->with('source')->andReturnNull();

        $job = new ScriptBuilder($this->process, $this->parser);
        $job->setup($this->deployment, $this->step, $this->release_archive, $this->private_key)
            ->buildScript($this->server);
    }

    public function provideDeploySteps()
    {
        return $this->fixture('Jobs/DeployProject/ScriptBuilder')['steps'];
    }

    /**
     * @covers ::__construct
     * @covers ::buildScript
     */
    public function testBuildScriptThrowsExceptionIsSetupNotCalled()
    {
        $this->expectException(Exception::class);

        $process = m::mock(Process::class);
        $parser  = m::mock(ScriptParser::class);
        $server  = m::mock(Server::class);

        $job = new ScriptBuilder($process, $parser);
        $job->buildScript($server);
    }
}
