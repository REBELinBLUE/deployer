<?php

namespace REBELinBLUE\Deployer\View\Presenters;

use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Command;
use Robbo\Presenter\Presenter;

/**
 * The view presenter for a command class.
 */
class CommandPresenter extends Presenter
{
    /**
     * Gets the readable list of before clone commands.
     *
     * @return string
     *
     * @see CommandPresenter::commandNames()
     */
    public function presentBeforeClone()
    {
        return $this->commandNames(Command::BEFORE_CLONE);
    }

    /**
     * Gets the readable list of after clone commands.
     *
     * @return string
     *
     * @see CommandPresenter::commandNames()
     */
    public function presentAfterClone()
    {
        return $this->commandNames(Command::AFTER_CLONE);
    }

    /**
     * Gets the readable list of before install commands.
     *
     * @return string
     *
     * @see CommandPresenter::commandNames()
     */
    public function presentBeforeInstall()
    {
        return $this->commandNames(Command::BEFORE_INSTALL);
    }

    /**
     * Gets the readable list of after install commands.
     *
     * @return string
     *
     * @see CommandPresenter::commandNames()
     */
    public function presentAfterInstall()
    {
        return $this->commandNames(Command::AFTER_INSTALL);
    }

    /**
     * Gets the readable list of before activate commands.
     *
     * @return string
     *
     * @see CommandPresenter::commandNames()
     */
    public function presentBeforeActivate()
    {
        return $this->commandNames(Command::BEFORE_ACTIVATE);
    }

    /**
     * Gets the readable list of after activate commands.
     *
     * @return string
     *
     * @see CommandPresenter::commandNames()
     */
    public function presentAfterActivate()
    {
        return $this->commandNames(Command::AFTER_ACTIVATE);
    }

    /**
     * Gets the readable list of before purge commands.
     *
     * @return string
     *
     * @see CommandPresenter::commandNames()
     */
    public function presentBeforePurge()
    {
        return $this->commandNames(Command::BEFORE_PURGE);
    }

    /**
     * Gets the readable list of after purge commands.
     *
     * @return string
     *
     * @see CommandPresenter::commandNames()
     */
    public function presentAfterPurge()
    {
        return $this->commandNames(Command::AFTER_PURGE);
    }

    /**
     * Gets the readable list of commands.
     *
     * @param int $stage
     *
     * @return string
     */
    private function commandNames($stage)
    {
        $commands = [];

        foreach ($this->getObject()->commands as $command) {
            if ($command->step === $stage) {
                $commands[] = $command->name;
            }
        }

        if (count($commands)) {
            return implode(', ', $commands);
        }

        return Lang::get('app.none');
    }
}
