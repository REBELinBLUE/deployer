<?php namespace App\Presenters;

use Lang;
use App\Project;
use App\Command;
use Robbo\Presenter\Presenter;

/**
 * The view presenter for a project class
 */
class ProjectPresenter extends Presenter
{
    /**
     * Returns the build status needed by CCTray
     * These strings can not be translated
     *
     * @return string
     */
    public function presentCcTrayStatus()
    {
        if ($this->status === Project::FINISHED || $this->status === Project::FAILED) {
            return 'Sleeping';
        } elseif ($this->status === Project::DEPLOYING) {
            return 'Building';
        } elseif ($this->status === Project::PENDING) {
            return 'Pending';
        }

        return 'Unknown';
    }

    /**
     * Gets the translated project status string
     *
     * @return string
     */
    public function presentReadableStatus()
    {
        if ($this->status === Project::FINISHED) {
            return Lang::get('projects.finished');
        } elseif ($this->status === Project::DEPLOYING) {
            return Lang::get('projects.deploying');
        } elseif ($this->status === Project::FAILED) {
            return Lang::get('projects.failed');
        } elseif ($this->status === Project::PENDING) {
            return Lang::get('projects.pending');
        }

        return Lang::get('projects.not_deployed');
    }

    /**
     * Gets the CSS icon class for the project status
     *
     * @return string
     */
    public function presentIcon()
    {
        if ($this->status === Project::FINISHED) {
            return 'check';
        } elseif ($this->status === Project::DEPLOYING) {
            // if (!$rotate) {
            //     return 'spinner';
            // }

            return 'spinner fa-pulse';
        } elseif ($this->status === Project::FAILED) {
            return 'warning';
        } elseif ($this->status === Project::PENDING) {
            return 'clock-o';
        }

        return 'question-circle';
    }

    /**
     * Gets the CSS class for the project status
     *
     * @return string
     */
    public function presentCssClass()
    {
        if ($this->status === Project::FINISHED) {
            return 'success';
        } elseif ($this->status === Project::DEPLOYING) {
            return 'warning';
        } elseif ($this->status === Project::FAILED) {
            return 'danger';
        } elseif ($this->status === Project::PENDING) {
            return 'info';
        }

        return 'primary';
    }

    /**
     * Gets the readable list of before clone commands
     *
     * @return string
     * @see self::commandNames()
     */
    public function presentBeforeClone()
    {
        return $this->commandNames(Command::BEFORE_CLONE);
    }

    /**
     * Gets the readable list of after clone commands
     *
     * @return string
     * @see self::commandNames()
     */
    public function presentAfterClone()
    {
        return $this->commandNames(Command::AFTER_CLONE);
    }

    /**
     * Gets the readable list of before install commands
     *
     * @return string
     * @see self::commandNames()
     */
    public function presentBeforeInstall()
    {
        return $this->commandNames(Command::BEFORE_INSTALL);
    }

    /**
     * Gets the readable list of after install commands
     *
     * @return string
     * @see self::commandNames()
     */
    public function presentAfterInstall()
    {
        return $this->commandNames(Command::AFTER_INSTALL);
    }

    /**
     * Gets the readable list of before activate commands
     *
     * @return string
     * @see self::commandNames()
     */
    public function presentBeforeActivate()
    {
        return $this->commandNames(Command::BEFORE_ACTIVATE);
    }

    /**
     * Gets the readable list of after activate commands
     *
     * @return string
     * @see self::commandNames()
     */
    public function presentAfterActivate()
    {
        return $this->commandNames(Command::AFTER_ACTIVATE);
    }

    /**
     * Gets the readable list of before purge commands
     *
     * @return string
     * @see self::commandNames()
     */
    public function presentBeforePurge()
    {
        return $this->commandNames(Command::BEFORE_PURGE);
    }

    /**
     * Gets the readable list of after purge commands
     *
     * @return string
     * @see self::commandNames()
     */
    public function presentAfterPurge()
    {
        return $this->commandNames(Command::AFTER_PURGE);
    }

    /**
     * Gets the readable list of commands
     *
     * @param int $stage
     * @return string
     */
    private function commandNames($stage)
    {
        $commands = [];

        foreach ($this->object->commands as $command) {
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
