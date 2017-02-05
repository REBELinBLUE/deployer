<?php

namespace REBELinBLUE\Deployer\Tests\Unit\stubs;

use REBELinBLUE\Deployer\View\Presenters\RuntimePresenter;
use Robbo\Presenter\Presenter as BasePresenter;

class Presenter extends BasePresenter
{
    use RuntimePresenter;
}
