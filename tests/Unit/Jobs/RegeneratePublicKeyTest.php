<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Mockery as m;
use REBELinBLUE\Deployer\Jobs\RegeneratePublicKey;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\RegeneratePublicKey
 */
class RegeneratePublicKeyTest extends TestCase
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
        $expectedProject    = 'my project';

        $this->filesystem->shouldReceive('tempnam')->once()->with($folder, 'key')->andReturn($expectedPath);
        $this->filesystem->shouldReceive('put')->once()->with($expectedPath, $expectedPrivateKey);
        $this->filesystem->shouldReceive('chmod')->with($expectedPath, 0600);
        $this->filesystem->shouldReceive('get')->once()->with($expectedPath . '.pub')->andReturn($expectedPublicKey);
        $this->filesystem->shouldReceive('delete')->once()->with([$expectedPath, $expectedPath . '.pub']);

        $this->process->shouldReceive('setScript')
                      ->with('tools.RegeneratePublicSSHKey', [
                          'key_file' => $expectedPath,
                          'project'  => $expectedProject,
                      ])
                      ->andReturnSelf();
        $this->process->shouldReceive('run')->once();
        $this->process->shouldReceive('isSuccessful')->andReturn(true);

        $project              = new Project();
        $project->private_key = $expectedPrivateKey;
        $project->name        = $expectedProject;

        $job = new RegeneratePublicKey($project);
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

        $expectedPrivateKey = 'a-private-key';
        $folder             = storage_path('app/tmp');
        $expectedPath       = $folder . 'sshkeyA-TMP-FILE-NAME';
        $expectedProject    = 'my project';

        $this->filesystem->shouldReceive('tempnam')->once()->with($folder, 'key')->andReturn($expectedPath);
        $this->filesystem->shouldReceive('put')->once()->with($expectedPath, $expectedPrivateKey);
        $this->filesystem->shouldReceive('chmod')->with($expectedPath, 0600);
        $this->filesystem->shouldReceive('delete')->once()->with([$expectedPath, $expectedPath . '.pub']);

        $this->process->shouldReceive('setScript')
                      ->with('tools.RegeneratePublicSSHKey', [
                          'key_file' => $expectedPath,
                          'project'  => $expectedProject,
                      ])
                      ->andReturnSelf();
        $this->process->shouldReceive('run')->once();
        $this->process->shouldReceive('isSuccessful')->andReturn(false);
        $this->process->shouldReceive('getErrorOutput');

        $project              = new Project();
        $project->private_key = $expectedPrivateKey;
        $project->name        = $expectedProject;

        $job = new RegeneratePublicKey($project);
        $job->handle($this->filesystem, $this->process);
    }
}
