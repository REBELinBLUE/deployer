<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Mockery as m;
use REBELinBLUE\Deployer\Jobs\GenerateKey;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\GenerateKey
 */
class GenerateKeyTest extends TestCase
{
    private $filesystem;
    private $process;

    public function setUp()
    {
        parent::setUp();

        $this->process    = m::mock(Process::class);
        $this->filesystem = m::mock(Filesystem::class);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle()
    {
        $folder = storage_path('app/tmp');

        $expectedPrivateKey = 'a-private-key';
        $expectedPublicKey  = 'a-public-key';
        $expectedPath       = $folder . 'sshkeyA-TMP-FILE-NAME';
        $expectedProject    = 'a project';

        $this->filesystem->shouldReceive('tempnam')->once()->with($folder, 'key')->andReturn($expectedPath);
        $this->filesystem->shouldReceive('get')->once()->with($expectedPath)->andReturn($expectedPrivateKey);
        $this->filesystem->shouldReceive('get')->once()->with($expectedPath . '.pub')->andReturn($expectedPublicKey);
        $this->filesystem->shouldReceive('delete')->once()->with([$expectedPath, $expectedPath . '.pub']);

        $this->process->shouldReceive('setScript')
                      ->with('tools.GenerateSSHKey', ['key_file' => $expectedPath, 'project' => $expectedProject])
                      ->andReturnSelf();
        $this->process->shouldReceive('run')->once();
        $this->process->shouldReceive('isSuccessful')->andReturn(true);

        $project       = new Project();
        $project->name = $expectedProject;

        $job = new GenerateKey($project);
        $job->handle($this->filesystem, $this->process);

        $this->assertSame($expectedPrivateKey, $project->private_key);
        $this->assertSame($expectedPublicKey, $project->public_key);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleShouldThrowExceptionOnFailure()
    {
        $this->expectException(RuntimeException::class);

        $folder          = storage_path('app/tmp');
        $expectedPath    = $folder . 'sshkeyA-TMP-FILE-NAME';
        $expectedProject = 'project name';

        $this->filesystem->shouldReceive('tempnam')->once()->with($folder, 'key')->andReturn($expectedPath);
        $this->filesystem->shouldReceive('delete')->once()->with([$expectedPath, $expectedPath . '.pub']);

        $this->process->shouldReceive('setScript')
                      ->with('tools.GenerateSSHKey', ['key_file' => $expectedPath, 'project' => $expectedProject])
                      ->andReturnSelf();
        $this->process->shouldReceive('run')->once();
        $this->process->shouldReceive('isSuccessful')->andReturn(false);
        $this->process->shouldReceive('getErrorOutput');

        $project       = new Project();
        $project->name = $expectedProject;

        $job = new GenerateKey($project);
        $job->handle($this->filesystem, $this->process);
    }
}
