<?php

namespace REBELinBLUE\Deployer\Tests\Unit\stubs;

use REBELinBLUE\Deployer\View\Presenters\Presenter as BasePresenter;
use REBELinBLUE\Deployer\View\Presenters\RuntimePresenter;

class Presenter extends BasePresenter
{
    use RuntimePresenter;

    public function presentFooBar(): string
    {
        return 'baz';
    }

    public function snake_case(): string
    {
        return 'bar';
    }
}
