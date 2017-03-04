<?php

namespace REBELinBLUE\Deployer\View\Presenters;

use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;

/**
 * The view presenter for a project class.
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
     * Gets the translated deployment status string.
     *
     * @return string
     */
    public function presentReadableStatus()
    {
        if ($this->status === Deployment::COMPLETED) {
            return $this->translator->trans('deployments.completed');
        } elseif ($this->status === Deployment::COMPLETED_WITH_ERRORS) {
            return $this->translator->trans('deployments.completed_with_errors');
        } elseif ($this->status === Deployment::ABORTING) {
            return $this->translator->trans('deployments.aborting');
        } elseif ($this->status === Deployment::ABORTED) {
            return $this->translator->trans('deployments.aborted');
        } elseif ($this->status === Deployment::FAILED) {
            return $this->translator->trans('deployments.failed');
        } elseif ($this->status === Deployment::DEPLOYING) {
            return $this->translator->trans('deployments.deploying');
        }

        return $this->translator->trans('deployments.pending');
    }

    /**
     * Gets the IDs of the optional commands which were included in the deployments, for use in a data attribute.
     *
     * @return string
     */
    public function presentOptionalCommandsUsed()
    {
        return $this->getWrappedObject()->commands->filter(function (Command $command) {
            return $command->optional;
        })->implode('id', ',');
    }

    /**
     * Gets the CSS icon class for the deployment status.
     *
     * @return string
     */
    public function presentIcon()
    {
        $finished_statuses = [Deployment::FAILED, Deployment::COMPLETED_WITH_ERRORS,
                              Deployment::ABORTING, Deployment::ABORTED, ];

        if ($this->status === Deployment::COMPLETED) {
            return 'check';
        } elseif (in_array($this->status, $finished_statuses, true)) {
            return 'warning';
        } elseif ($this->status === Deployment::DEPLOYING) {
            return 'spinner fa-pulse';
        }

        return 'clock-o';
    }

    /**
     * Gets the CSS class for the deployment status.
     *
     * @return string
     */
    public function presentCssClass()
    {
        if ($this->status === Deployment::COMPLETED || $this->status === Deployment::COMPLETED_WITH_ERRORS) {
            return 'success';
        } elseif (in_array($this->status, [Deployment::FAILED, Deployment::ABORTING, Deployment::ABORTED], true)) {
            return 'danger';
        } elseif ($this->status === Deployment::DEPLOYING) {
            return 'warning';
        }

        return 'info';
    }

    /**
     * Gets the CSS class for the deployment status for the timeline.
     *
     * @return string
     */
    public function presentTimelineCssClass()
    {
        if ($this->status === Deployment::COMPLETED || $this->status === Deployment::COMPLETED_WITH_ERRORS) {
            return 'green';
        } elseif (in_array($this->status, [Deployment::FAILED, Deployment::ABORTING, Deployment::ABORTED], true)) {
            return 'red';
        } elseif ($this->status === Deployment::DEPLOYING) {
            return 'yellow';
        }

        return 'aqua';
    }

    /**
     * Gets the name of the committer, or the "Loading" string if it has not yet been determined.
     *
     * @return string
     */
    public function presentCommitterName()
    {
        if ($this->committer === Deployment::LOADING) {
            if ($this->status === Deployment::FAILED) {
                return $this->translator->trans('deployments.unknown');
            }

            return $this->translator->trans('deployments.loading');
        }

        return $this->committer;
    }

    /**
     * Gets the short commit hash, or the "Loading" string if it has not yet been determined.
     *
     * @return string
     */
    public function presentShortCommitHash()
    {
        if ($this->short_commit === Deployment::LOADING) {
            if ($this->status === Deployment::FAILED) {
                return $this->translator->trans('deployments.unknown');
            }

            return $this->translator->trans('deployments.loading');
        }

        return $this->short_commit;
    }
}
