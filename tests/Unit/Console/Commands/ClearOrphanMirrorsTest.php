<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Closure;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\ClearOrphanMirrors;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\ClearOrphanMirrors
 */
class ClearOrphanMirrorsTest extends TestCase
{
    private $filesystem;

    private $console;

    private $repository;

    public function setUp()
    {
        parent::setUp();

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $filesystem = m::mock(Filesystem::class);

        $repository = m::mock(ProjectRepositoryInterface::class);

        $mirror_dir = storage_path('app/mirrors');

        $repository->shouldReceive('chunk')->with(100, m::on(function ($callback) use ($mirror_dir) {
            $this->assertInstanceOf(Closure::class, $callback);

            $project = m::mock(Project::class);
            $project->shouldReceive('mirrorPath')->andReturn($mirror_dir . '/project.git');

            $callback(collect([$project]));

            return true;
        }));

        $filesystem->shouldReceive('glob')
                   ->with($mirror_dir . '/*.git')
                   ->andReturn([
                       $mirror_dir . '/project.git',
                       $mirror_dir . '/second-project.git',
                   ]);

        $this->filesystem = $filesystem;
        $this->console    = $console;
        $this->repository = $repository;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle()
    {
        $mirror_dir = storage_path('app/mirrors') . '/second-project.git';

        $this->filesystem->shouldReceive('basename')->with($mirror_dir)->andReturn('second-project.git');
        $this->filesystem->shouldReceive('deleteDirectory')->with($mirror_dir)->andReturn(true);

        $command = new ClearOrphanMirrors($this->repository, $this->filesystem);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:purge-mirrors',
        ]);

        $output = $tester->getDisplay();

        $this->assertContains('Found 1 orphaned mirrors', $output);
        $this->assertContains('Deleted second-project.git', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleDeletionFailure()
    {
        $mirror_dir = storage_path('app/mirrors') . '/second-project.git';

        $this->filesystem->shouldReceive('basename')->with($mirror_dir)->andReturn('second-project.git');
        $this->filesystem->shouldReceive('deleteDirectory')->with($mirror_dir)->andReturn(false);

        $command = new ClearOrphanMirrors($this->repository, $this->filesystem);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:purge-mirrors',
        ]);

        $output = $tester->getDisplay();

        $this->assertContains('Found 1 orphaned mirrors', $output);
        $this->assertContains('Failed to delete second-project.git', $output);
        $this->assertNotContains('Deleted second-project.git', $output);
    }
}
