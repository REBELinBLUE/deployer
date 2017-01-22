<?php

namespace REBELinBLUE\Deployer\View\Presenters;

use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Project;

/**
 * The view presenter for a project class.
 * @property int status
 * @property string readable_status
 */
class ProjectPresenter extends CommandPresenter
{
    /**
     * Returns the build status needed by CCTray
     * These strings can not be translated.
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
     * Gets the translated project status string.
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
     * Gets the CSS icon class for the project status.
     *
     * @return string
     */
    public function presentIcon()
    {
        if ($this->status === Project::FINISHED) {
            return 'check';
        } elseif ($this->status === Project::DEPLOYING) {
            return 'spinner fa-pulse';
        } elseif ($this->status === Project::FAILED) {
            return 'warning';
        } elseif ($this->status === Project::PENDING) {
            return 'clock-o';
        }

        return 'question-circle';
    }

    /**
     * Gets the CSS class for the project status.
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
     * Show the application status.
     *
     * @return string
     */
    public function presentAppStatus()
    {
        $status = $this->applicationCheckUrlStatus();

        if ($status['length'] === 0) {
            return Lang::get('app.not_applicable');
        }

        return ($status['length'] - $status['missed']) . ' / ' . $status['length'];
    }

    /**
     * Show the application status css.
     *
     * @return string
     */
    public function presentAppStatusCss()
    {
        $status = $this->applicationCheckUrlStatus();

        if ($status['length'] === 0) {
            return 'warning';
        } elseif ($status['missed']) {
            return 'danger';
        }

        return 'success';
    }

    /**
     * Show heartbeat status count.
     *
     * @return string
     */
    public function presentHeartBeatStatus()
    {
        $status = $this->heartbeatsStatus();

        if ($status['length'] === 0) {
            return Lang::get('app.not_applicable');
        }

        return ($status['length'] - $status['missed']) . ' / ' . $status['length'];
    }

    /**
     * The application heartbeat status css.
     *
     * @return string
     */
    public function presentHeartBeatStatusCss()
    {
        $status = $this->heartbeatsStatus();

        if ($status['length'] === 0) {
            return 'warning';
        } elseif ($status['missed']) {
            return 'danger';
        }

        return 'success';
    }

    /**
     * Gets an icon which represents the repository type.
     *
     * @return string
     */
    public function presentTypeIcon()
    {
        $details = $this->accessDetails();

        if (isset($details['domain'])) {
            if (preg_match('/github\.com/', $details['domain'])) {
                return 'fa-github';
            } elseif (preg_match('/gitlab\.com/', $details['domain'])) {
                return 'fa-gitlab';
            } elseif (preg_match('/bitbucket/', $details['domain'])) {
                return 'fa-bitbucket';
            } elseif (preg_match('/amazonaws\.com/', $details['domain'])) {
                return 'fa-amazon';
            }
        }

        return 'fa-git-square';
    }
}
