<?php

namespace REBELinBLUE\Deployer\Presenters;

use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use Robbo\Presenter\Presenter;

/**
 * The view presenter for a project class.
 * @property string committer_name
 * @property integer status
 * @property string short_commit
 * @property string committer
 */
class DeploymentPresenter extends Presenter
{
    use RuntimePresenter;

    /**
     * Returns the build status needed by CCTray
     * These strings can not be translated.
     *
     * @return string
     */
    public function presentCcTrayStatus()
    {
        if ($this->status === Deployment::COMPLETED || $this->status === Deployment::COMPLETED_WITH_ERRORS) {
            return 'Success';
        } elseif ($this->status === Deployment::FAILED || $this->status === Deployment::ABORTED) {
            return 'Failure';
        }

        return 'Unknown';
    }

    /**
     * Gets the IDs of the optional commands which were included in the deployments, for use in a data attribute.
     *
     * @return string
     */
    public function presentOptionalCommandsUsed()
    {
        return $this->getObject()->commands->filter(function (Command $command) {
            return $command->optional;
        })->implode('id', ',');
    }

    /**
     * Gets the name of the committer, or the "Loading" string if it has not yet been determined.
     *
     * @return string
     */
    public function presentCommitterName() // FIXME: Implement this in the component
    {
        if ($this->committer === Deployment::LOADING) {
            if ($this->status === Deployment::FAILED) {
                return Lang::get('deployments.unknown');
            }

            return Lang::get('deployments.loading');
        }

        return $this->committer;
    }

    /**
     * Gets the short commit hash, or the "Loading" string if it has not yet been determined.
     *
     * @return string
     */
    public function presentShortCommitHash()// FIXME: Implement this in the component
    {
        if ($this->short_commit === Deployment::LOADING) {
            if ($this->status === Deployment::FAILED) {
                return Lang::get('deployments.unknown');
            }

            return Lang::get('deployments.loading');
        }

        return $this->short_commit;
    }
}
