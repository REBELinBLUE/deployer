<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Carbon\Carbon;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\ClearOldKeys;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\ClearOldKeys
 */
class ClearOldKeysTest extends TestCase
{
    private $filesystem;

    private $console;

    protected function setUp(): void
    {
        parent::setUp();

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $filesystem = m::mock(Filesystem::class);

        $tmp_dir = storage_path('app/tmp');
        $app_dir = storage_path('app');

        $filesystem->shouldReceive('glob')->with($tmp_dir . '/*key*')->andReturn(['/var/www/gitkeys']);
        $filesystem->shouldReceive('glob')->with($tmp_dir . '/*tmp*')->andReturn(['/var/www/tmpfile']);
        $filesystem->shouldReceive('glob')->with($tmp_dir . '/*ssh*')->andReturn(['/var/www/sshwrapper']);
        $filesystem->shouldReceive('glob')->with($app_dir . '/*.tar.gz')->andReturn(['/var/www/mirror.tar.gz']);
        $filesystem->shouldReceive('glob')->with($tmp_dir . '/clone_*')->andReturn(['/var/www/clone_mirror']);

        $filesystem->shouldReceive('basename')->with('/var/www/gitkeys')->andReturn('gitkeys');
        $filesystem->shouldReceive('basename')->with('/var/www/tmpfile')->andReturn('tmpfile');
        $filesystem->shouldReceive('basename')->with('/var/www/sshwrapper')->andReturn('sshwrapper');
        $filesystem->shouldReceive('basename')->with('/var/www/mirror.tar.gz')->andReturn('mirror.tar.gz');
        $filesystem->shouldReceive('basename')->with('/var/www/clone_mirror')->andReturn('clone_mirror');

        $filesystem->shouldReceive('isDirectory')->with('/var/www/gitkeys')->andReturn(false);
        $filesystem->shouldReceive('isDirectory')->with('/var/www/tmpfile')->andReturn(false);
        $filesystem->shouldReceive('isDirectory')->with('/var/www/sshwrapper')->andReturn(false);
        $filesystem->shouldReceive('isDirectory')->with('/var/www/mirror.tar.gz')->andReturn(false);
        $filesystem->shouldReceive('isDirectory')->with('/var/www/clone_mirror')->andReturn(true);

        $this->filesystem = $filesystem;
        $this->console    = $console;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle()
    {
        Carbon::setTestNow(Carbon::create(2017, 1, 1, 12, 00, 00, 'UTC'));

        $timestamp = Carbon::create(2015, 1, 1, 12, 00, 00, 'UTC')->timestamp;

        $this->filesystem->shouldReceive('lastModified')->andReturn($timestamp);

        $this->filesystem->shouldReceive('deleteDirectory')->with('/var/www/clone_mirror')->andReturn(true);
        $this->filesystem->shouldReceive('delete')->with('/var/www/gitkeys')->andReturn(true);
        $this->filesystem->shouldReceive('delete')->with('/var/www/tmpfile')->andReturn(true);
        $this->filesystem->shouldReceive('delete')->with('/var/www/sshwrapper')->andReturn(true);
        $this->filesystem->shouldReceive('delete')->with('/var/www/mirror.tar.gz')->andReturn(true);

        $command = new ClearOldKeys($this->filesystem);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:purge-temp',
        ]);

        $output = $tester->getDisplay();

        $this->assertStringContainsString('Found 4 files and 1 folders to purge', $output);
        $this->assertStringContainsString('Deleted gitkeys', $output);
        $this->assertStringContainsString('Deleted tmpfile', $output);
        $this->assertStringContainsString('Deleted sshwrapper', $output);
        $this->assertStringContainsString('Deleted mirror.tar.gz', $output);
        $this->assertStringContainsString('Deleted clone_mirror', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleSkipsRecentFiles()
    {
        Carbon::setTestNow(Carbon::create(2017, 1, 1, 12, 00, 00, 'UTC'));

        $timestamp = Carbon::create(2017, 1, 1, 23, 59, 59)->timestamp;

        $this->filesystem->shouldReceive('lastModified')->andReturn($timestamp);
        $this->filesystem->shouldNotReceive('delete');
        $this->filesystem->shouldNotReceive('deleteDirectory');

        $command = new ClearOldKeys($this->filesystem);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:purge-temp',
        ]);

        $output = $tester->getDisplay();

        $this->assertStringContainsString('Found 4 files and 1 folders to purge', $output);
        $this->assertStringContainsString('Skipping gitkeys', $output);
        $this->assertStringContainsString('Skipping tmpfile', $output);
        $this->assertStringContainsString('Skipping sshwrapper', $output);
        $this->assertStringContainsString('Skipping mirror.tar.gz', $output);
        $this->assertStringContainsString('Skipping clone_mirror', $output);
        $this->assertStringNotContainsString('Deleted gitkeys', $output);
        $this->assertStringNotContainsString('Deleted tmpfile', $output);
        $this->assertStringNotContainsString('Deleted sshwrapper', $output);
        $this->assertStringNotContainsString('Deleted mirror.tar.gz', $output);
        $this->assertStringNotContainsString('Deleted clone_mirror', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleDeletionFailure()
    {
        Carbon::setTestNow(Carbon::create(2016, 5, 4, 11, 00, 00, 'UTC'));

        $timestamp = Carbon::create(2015, 1, 1, 12, 00, 00, 'UTC')->timestamp;

        $this->filesystem->shouldReceive('lastModified')->andReturn($timestamp);

        $this->filesystem->shouldReceive('deleteDirectory')->with('/var/www/clone_mirror')->andReturn(false);
        $this->filesystem->shouldReceive('delete')->with('/var/www/gitkeys')->andReturn(false);
        $this->filesystem->shouldReceive('delete')->with('/var/www/sshwrapper')->andReturn(false);
        $this->filesystem->shouldReceive('delete')->with('/var/www/tmpfile')->andReturn(false);
        $this->filesystem->shouldReceive('delete')->with('/var/www/mirror.tar.gz')->andReturn(false);

        $command = new ClearOldKeys($this->filesystem);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:purge-temp',
        ]);

        $output = $tester->getDisplay();

        $this->assertStringContainsString('Found 4 files and 1 folders to purge', $output);
        $this->assertStringContainsString('Failed to delete file gitkeys', $output);
        $this->assertStringContainsString('Failed to delete file sshwrapper', $output);
        $this->assertStringContainsString('Failed to delete file tmpfile', $output);
        $this->assertStringContainsString('Failed to delete file mirror.tar.gz', $output);
        $this->assertStringContainsString('Failed to delete folder clone_mirror', $output);
        $this->assertStringNotContainsString('Deleted gitkeys', $output);
        $this->assertStringNotContainsString('Deleted tmpfile', $output);
        $this->assertStringNotContainsString('Deleted sshwrapper', $output);
        $this->assertStringNotContainsString('Deleted mirror.tar.gz', $output);
        $this->assertStringNotContainsString('Deleted clone_mirror', $output);
    }
}
