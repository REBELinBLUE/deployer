<?php

namespace REBELinBLUE\Deployer\Tests\Unit\stubs;

use Illuminate\Database\Eloquent\Model as BaseModel;
use McCool\LaravelAutoPresenter\HasPresenter;
use REBELinBLUE\Deployer\View\Presenters\RuntimeInterface;

class Model extends BaseModel implements HasPresenter, RuntimeInterface
{
    public function runtime()
    {
        return 0;
    }

    public function getPresenterClass()
    {
        return StubPresenter::class;
    }
}
