<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Illuminate\Console\Command;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

abstract class CommandTestCase extends TestCase
{
    protected function runCommand(Command $command, $input = [])
    {
        $output = new BufferedOutput();

        $command->run(new ArrayInput($input), $output);

        return $output->fetch();
    }
}
