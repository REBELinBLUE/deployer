<?php

use REBELinBLUE\Deployer\View\Presenters\ServerLogPresenter;

class ServerLogPresenterTest extends TestCase
{
    public function testRuntimeInterfaceIsUsed()
    {
        $this->expectException(\RuntimeException::class);

        // No object should throw an exception
        $presenter = new ServerLogPresenter(null);
        $presenter->presentReadableRuntime();

        // Class which doesn't implement the RuntimeInterface
        $presenter = new ServerLogPresenter(new stdClass);
        $presenter->presentReadableRuntime();
    }
}
