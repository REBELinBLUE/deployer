<?php namespace App\Presenters;

use Lang;
use App\Deployment;
use Robbo\Presenter\Presenter;

/**
 * The view presenter for a project class
 */
class DeploymentPresenter extends Presenter
{
    /**
     * Gets the translated deployment status string
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
     * Gets the CSS icon class for the deployment status
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
            return 'spinner fa-spin';
        }

        return 'clock-o';
    }

    /**
     * Gets the CSS class for the deployment status
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
     * Gets the CSS class for the deployment status for the timeline
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
}