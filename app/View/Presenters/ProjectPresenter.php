<?php

namespace REBELinBLUE\Deployer\View\Presenters;

use REBELinBLUE\Deployer\Project;

/**
 * The view presenter for a project class.
 */
class ProjectPresenter extends CommandPresenter
{
    /**
     * Returns the build status needed by CCTray
     * These strings can not be translated.
     *
     * @return string
     */
    public function presentCcTrayStatus(): string
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
    public function presentReadableStatus(): string
    {
        if ($this->status === Project::FINISHED) {
            return $this->translator->get('projects.finished');
        } elseif ($this->status === Project::DEPLOYING) {
            return $this->translator->get('projects.deploying');
        } elseif ($this->status === Project::FAILED) {
            return $this->translator->get('projects.failed');
        } elseif ($this->status === Project::PENDING) {
            return $this->translator->get('projects.pending');
        }

        return $this->translator->get('projects.not_deployed');
    }

    /**
     * Gets the CSS icon class for the project status.
     *
     * @return string
     */
    public function presentIcon(): string
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
    public function presentCssClass(): string
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
    public function presentAppStatus(): string
    {
        $status = $this->applicationCheckUrlStatus();

        return $this->getStatusLabel($status);
    }

    /**
     * Show the application status css.
     *
     * @return string
     */
    public function presentAppStatusCss(): string
    {
        $status = $this->applicationCheckUrlStatus();

        return $this->getStatusCss($status);
    }

    /**
     * Show heartbeat status count.
     *
     * @return string
     */
    public function presentHeartBeatStatus(): string
    {
        $status = $this->heartbeatsStatus();

        return $this->getStatusLabel($status);
    }

    /**
     * The application heartbeat status css.
     *
     * @return string
     */
    public function presentHeartBeatStatusCss(): string
    {
        $status = $this->heartbeatsStatus();

        return $this->getStatusCss($status);
    }

    /**
     * Gets an icon which represents the repository type.
     *
     * @return string
     */
    public function presentTypeIcon(): string
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

    /**
     * Gets the status CSS class for heartbeats and URLs.
     *
     * @param array $status
     *
     * @return string
     */
    private function getStatusCss(array $status): string
    {
        if ($status['length'] === 0) {
            return 'warning';
        } elseif ($status['missed']) {
            return 'danger';
        }

        return 'success';
    }

    /**
     * Gets the status label for heartbeats and URLs.
     *
     * @param array $status
     *
     * @return string
     */
    private function getStatusLabel(array $status): string
    {
        if ($status['length'] === 0) {
            return $this->translator->get('app.not_applicable');
        }

        return ($status['length'] - $status['missed']) . ' / ' . $status['length'];
    }
}
