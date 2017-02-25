<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Illuminate\Support\Composer;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\MakeRepositoryCommand;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\MakeRepositoryCommand
 */
class MakeRepositoryCommandTest extends TestCase
{
    private $filesystem;
    private $composer;
    private $console;
    private $concrete;
    private $contract;

    public function setUp()
    {
        parent::setUp();

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $filesystem = m::mock(Filesystem::class);
        $composer   = m::mock(Composer::class);

        $this->console    = $console;
        $this->filesystem = $filesystem;
        $this->composer   = $composer;

        $this->contract = 'Repositories/Contracts/FooBarRepositoryInterface.php';
        $this->concrete = 'Repositories/EloquentFooBarRepository.php';
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::makeRepository
     * @covers ::createContract
     * @covers ::createConcrete
     * @covers ::createFile
     */
    public function testHandle()
    {
        $contract = app_path($this->contract);
        $concrete = app_path($this->concrete);

        $this->filesystem->shouldReceive('exists')->with($contract)->andReturn(false);
        $this->filesystem->shouldReceive('exists')->with($concrete)->andReturn(false);

        $this->filesystem->shouldReceive('put')->with($contract, $this->contractData());
        $this->filesystem->shouldReceive('put')->with($concrete, $this->concreteData());

        $this->composer->shouldReceive('dumpAutoloads');

        $command = new MakeRepositoryCommand($this->filesystem, $this->composer);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'make:repository',
            'name'    => 'FooBar',
        ]);

        $output = $tester->getDisplay();

        $this->assertContains($this->contract . ' created successfully.', $output);
        $this->assertContains($this->concrete . ' created successfully.', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::makeRepository
     */
    public function testHandleShouldReturnErrorWhenFileExists()
    {
        $contract = app_path($this->contract);

        $this->filesystem->shouldReceive('exists')->with($contract)->andReturn(true);

        $this->filesystem->shouldNotReceive('put');

        $this->composer->shouldNotReceive('dumpAutoloads');

        $command = new MakeRepositoryCommand($this->filesystem, $this->composer);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'make:repository',
            'name'    => 'FooBar',
        ]);

        $output = $tester->getDisplay();

        $this->assertContains($this->contract . ' already exists!', $output);
        $this->assertNotContains('created successfully.', $output);
    }

    private function concreteData()
    {
        return '<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\FooBarRepositoryInterface;

class EloquentFooBarRepository extends EloquentRepository implements FooBarRepositoryInterface
{

}
';
    }

    private function contractData()
    {
        return '<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

interface FooBarRepositoryInterface
{

}
';
    }
}
