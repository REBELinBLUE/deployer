<?php

namespace REBELinBLUE\Deployer\Tests\View\Presenters\Stubs;

use REBELinBLUE\Deployer\Tests\View\Presenters\Stubs\StubPresenter;
use REBELinBLUE\Deployer\View\Presenters\RuntimeInterface;
use Robbo\Presenter\PresentableInterface;

class StubModel implements PresentableInterface, RuntimeInterface
{
    public function runtime()
    {
        return 0;
    }

    public function getPresenter()
    {
        return new StubPresenter($this);
    }
}
