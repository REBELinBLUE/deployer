<?php namespace App\Presenters;

use Lang;
use App\Command;
use Robbo\Presenter\Presenter;

/**
 * The view presenter for a deploy step class
 */
class DeployStepPresenter extends Presenter
{
    /**
     * Gets the deployment stage label from the numeric representation
     *
     * @return string
     */
    public function presentName()
    {
        if ($this->stage === Command::DO_INSTALL) {
            return Lang::get('commands.install');
        } elseif ($this->stage === Command::DO_ACTIVATE) {
            return Lang::get('commands.activate');
        } elseif ($this->stage === Command::DO_PURGE) {
            return Lang::get('commands.purge');
        }

        return Lang::get('commands.clone');
    }
}