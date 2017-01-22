<?php

namespace REBELinBLUE\Deployer\View\Presenters;

use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Command;
use Robbo\Presenter\Presenter;

/**
 * The view presenter for a deploy step class.
 * @property int command_id
 * @property Command command
 * @property int stage
 */
class DeployStepPresenter extends Presenter
{
    /**
     * Gets the deployment stage label from the numeric representation.
     *
     * @return string
     */
    public function presentName()
    {
        if (!is_null($this->command_id)) {
            return $this->command->name;
        } elseif ($this->stage === Command::DO_INSTALL) {
            return Lang::get('commands.install');
        } elseif ($this->stage === Command::DO_ACTIVATE) {
            return Lang::get('commands.activate');
        } elseif ($this->stage === Command::DO_PURGE) {
            return Lang::get('commands.purge');
        }

        return Lang::get('commands.clone');
    }
}
