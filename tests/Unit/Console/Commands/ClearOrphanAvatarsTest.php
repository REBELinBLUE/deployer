<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\ClearOrphanAvatars;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\ClearOrphanAvatars
 */
class ClearOrphanAvatarsTest extends TestCase
{
    private $filesystem;

    private $console;

    private $database;

    public function setUp()
    {
        parent::setUp();

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $filesystem = m::mock(Filesystem::class);

        $database = m::mock(Connection::class);

        $public = public_path();

        $filesystem->shouldReceive('glob')
                   ->with($public . '/storage/*/*.*')
                   ->andReturn([
                       $public . '/storage/2017-01-01/avatar.png',
                       $public . '/storage/2017-02-01/second-avatar.jpg',
                       $public . '/storage/2016-01-01/third-avatar.gif',
                   ]);

        $current = new Collection(['/storage/2016-01-01/third-avatar.gif']);

        $database->shouldReceive('table')->with('users')->andReturnSelf();
        $database->shouldReceive('whereNotNull')->with('avatar')->andReturnSelf();
        $database->shouldReceive('pluck')->with('avatar')->andReturn($current);

        $this->filesystem = $filesystem;
        $this->console    = $console;
        $this->database   = $database;
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

        $this->filesystem->shouldReceive('delete')
                         ->with(public_path() . '/storage/2017-01-01/avatar.png')
                         ->andReturn(true);

        $this->filesystem->shouldReceive('delete')
                         ->with(public_path() . '/storage/2017-02-01/second-avatar.jpg')
                         ->andReturn(true);

        $command = new ClearOrphanAvatars($this->filesystem, $this->database);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:purge-avatars',
        ]);

        $output = $tester->getDisplay();

        $this->assertContains('Found 2 orphaned avatars', $output);
        $this->assertContains('Deleted /storage/2017-01-01/avatar.png', $output);
        $this->assertContains('Deleted /storage/2017-02-01/second-avatar.jpg', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleSkipsRecentFiles()
    {
        Carbon::setTestNow(Carbon::create(2017, 1, 1, 12, 00, 00, 'UTC'));

        $timestamp = Carbon::create(2017, 1, 1, 12, 14, 59)->timestamp;

        $this->filesystem->shouldReceive('lastModified')->andReturn($timestamp);
        $this->filesystem->shouldNotReceive('delete');

        $command = new ClearOrphanAvatars($this->filesystem, $this->database);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:purge-temp',
        ]);

        $output = $tester->getDisplay();

        $this->assertContains('Found 2 orphaned avatars', $output);
        $this->assertContains('Skipping /storage/2017-01-01/avatar.png', $output);
        $this->assertContains('Skipping /storage/2017-02-01/second-avatar.jpg', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleDeletionFailure()
    {
        Carbon::setTestNow(Carbon::create(2017, 1, 1, 12, 00, 00, 'UTC'));

        $timestamp = Carbon::create(2015, 1, 1, 11, 30, 00, 'UTC')->timestamp;

        $this->filesystem->shouldReceive('lastModified')->andReturn($timestamp);

        $this->filesystem->shouldReceive('deleteDirectory')->with('/var/www/clone_mirror')->andReturn(false);

        $this->filesystem->shouldReceive('delete')
                         ->with(public_path() . '/storage/2017-01-01/avatar.png')
                         ->andReturn(false);

        $this->filesystem->shouldReceive('delete')
                         ->with(public_path() . '/storage/2017-02-01/second-avatar.jpg')
                         ->andReturn(false);

        $command = new ClearOrphanAvatars($this->filesystem, $this->database);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:purge-temp',
        ]);

        $output = $tester->getDisplay();

        $this->assertContains('Found 2 orphaned avatars', $output);
        $this->assertContains('Failed to delete /storage/2017-01-01/avatar.png', $output);
        $this->assertContains('Failed to delete /storage/2017-02-01/second-avatar.jpg', $output);
    }
}
