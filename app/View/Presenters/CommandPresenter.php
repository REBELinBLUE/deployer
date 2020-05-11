<?php

namespace REBELinBLUE\Deployer\View\Presenters;

use REBELinBLUE\Deployer\Command;

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
    public function presentBeforeClone(): string
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
    public function presentAfterClone(): string
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
    public function presentBeforeInstall(): string
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
    public function presentAfterInstall(): string
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
    public function presentBeforeActivate(): string
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
    public function presentAfterActivate(): string
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
    public function presentBeforePurge(): string
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
    public function presentAfterPurge(): string
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
    private function commandNames(int $stage): string
    {
        $commands = [];

        foreach ($this->getWrappedObject()->commands as $command) {
            if ($command->step === $stage) {
                $commands[] = $command->name;
            }
        }

        if (count($commands)) {
            return implode(', ', $commands);
        }

        return $this->translator->get('app.none');
    }
}
