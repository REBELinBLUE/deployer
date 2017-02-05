<?php

namespace REBELinBLUE\Deployer\Tests\Unit\stubs;

use Illuminate\Database\Eloquent\Model as BaseModel;
use REBELinBLUE\Deployer\View\Presenters\RuntimeInterface;
use Robbo\Presenter\PresentableInterface;

class Model extends BaseModel implements PresentableInterface, RuntimeInterface
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
