<?php

namespace REBELinBLUE\Deployer\Presenters;

use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Presenters\Traits\RuntimePresenter;
use Robbo\Presenter\Presenter;

/**
 * The view presenter for a server class.
 */
class ServerLogPresenter extends Presenter
{
    use RuntimePresenter;
}
