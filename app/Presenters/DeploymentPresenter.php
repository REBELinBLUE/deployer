<?php

namespace App\Presenters;

use App\Deployment;
use Lang;
use Robbo\Presenter\Presenter;

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
        if ($this->status === Deployment::COMPLETED) {
            return 'Success';
        } elseif ($this->status === Deployment::FAILED) {
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
            return Lang::get('deployments.completed');
        } elseif ($this->status === Deployment::FAILED) {
            return Lang::get('deployments.failed');
        } elseif ($this->status === Deployment::DEPLOYING) {
            return Lang::get('deployments.deploying');
        }

        return Lang::get('deployments.pending');
    }

    /**
     * Gets the CSS icon class for the deployment status.
     *
     * @return string
     */
    public function presentIcon()
    {
        if ($this->status === Deployment::COMPLETED) {
            return 'check';
        } elseif ($this->status === Deployment::FAILED) {
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
        if ($this->status === Deployment::COMPLETED) {
            return 'success';
        } elseif ($this->status === Deployment::FAILED) {
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
        if ($this->status === Deployment::COMPLETED) {
            return 'green';
        } elseif ($this->status === Deployment::FAILED) {
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
            return Lang::get('deployments.loading');
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
        if ($this->committer === Deployment::LOADING) {
            return Lang::get('deployments.loading');
        }

        return $this->object->shortCommit();
    }
}
