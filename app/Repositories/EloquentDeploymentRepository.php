<?php

namespace App\Repositories;

use App\Deployment;
use App\Project;
use App\Repositories\Contracts\DeploymentRepositoryInterface;
use Carbon\Carbon;

/**
 * The deployment repository.
 */
class EloquentDeploymentRepository implements DeploymentRepositoryInterface
{
    /**
     * Gets the latest deployments for a project.
     *
     * @param  Project $project
     * @return array
     */
    public function getLatest(Project $project)
    {
        return Deployment::where('project_id', $project->id)
            ->with('user', 'project')
            ->orderBy('started_at', 'DESC')
            ->paginate($project->builds_to_keep);
    }

    /**
     * Gets the latest deployments for all projects.
     *
     * @return array
     */
    public function getTimeline()
    {
        $raw_sql = 'project_id IN (SELECT id FROM projects WHERE deleted_at IS NULL)';

        return Deployment::whereRaw($raw_sql)
            ->with('project')
            ->take(15)
            ->orderBy('started_at', 'DESC')
            ->get();
    }

    /**
     * Gets pending deployments.
     *
     * @return array
     */
    public function getPending()
    {
        return $this->getStatus(Deployment::PENDING);
    }

    /**
     * Gets running deployments.
     *
     * @return array
     */
    public function getRunning()
    {
        return $this->getStatus(Deployment::DEPLOYING);
    }

    /**
     * Gets the number of times a project has been deployed today.
     *
     * @param  Project $project
     * @return int
     * @see DeploymentRepository::getBetweenDates()
     */
    public function getTodayCount(Project $project)
    {
        $now = Carbon::now();

        return $this->getBetweenDates($project, $now, $now);
    }

    /**
     * Gets the number of times a project has been deployed in the last week.
     *
     * @param  Project $project
     * @return int
     * @see DeploymentRepository::getBetweenDates()
     */
    public function getLastWeekCount(Project $project)
    {
        $lastWeek  = Carbon::now()->subWeek();
        $yesterday = Carbon::now()->yesterday();

        return $this->getBetweenDates($project, $lastWeek, $yesterday);
    }

    /**
     * Gets the number of times a project has been deployed between the specified dates.
     *
     * @param  Project $project
     * @param  Carbon  $startDate
     * @param  Carbon  $endDate
     * @return int
     */
    private function getBetweenDates(Project $project, Carbon $startDate, Carbon $endDate)
    {
        return Deployment::where('project_id', $project->id)
            ->where('started_at', '>=', $startDate->format('Y-m-d') . ' 00:00:00')
            ->where('started_at', '<=', $endDate->format('Y-m-d') . ' 23:59:59')
            ->count();
    }

    /**
     * Gets deployments with a supplied status.
     *
     * @param  int   $status
     * @return array
     */
    private function getStatus($status)
    {
        $raw_sql = 'project_id IN (SELECT id FROM projects WHERE deleted_at IS NULL)';

        return Deployment::whereRaw($raw_sql)
            ->where('status', $status)
            ->orderBy('started_at', 'DESC')
            ->get();
    }
}
