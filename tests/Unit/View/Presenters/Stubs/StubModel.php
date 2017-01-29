<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Presenters\Stubs;

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
