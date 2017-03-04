<?php

namespace REBELinBLUE\Deployer\View\Presenters;

use REBELinBLUE\Deployer\Command;

/**
 * The view presenter for a deploy step class.
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
            return $this->translator->trans('commands.install');
        } elseif ($this->stage === Command::DO_ACTIVATE) {
            return $this->translator->trans('commands.activate');
        } elseif ($this->stage === Command::DO_PURGE) {
            return $this->translator->trans('commands.purge');
        }

        return $this->translator->trans('commands.clone');
    }
}
