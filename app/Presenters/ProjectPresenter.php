<?php namespace App\Presenters;

use Lang;
use App\Project;
use Robbo\Presenter\Presenter;

/**
 * The view presenter for a project class
 */
class ProjectPresenter extends Presenter
{
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

            return 'spinner fa-spin';
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
}
