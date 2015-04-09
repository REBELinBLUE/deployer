<?php namespace App\Repositories;

use App\Project;
use App\Deployment;

use Carbon\Carbon;

class DeploymentRepository
{
    public function getLatest(Project $project)
    {
        return Deployment::where('project_id', $project->id)
                         ->take($project->builds_to_keep)
                         ->orderBy('started_at', 'DESC')
                         ->get();
    }

    public function getTimeline()
    {
        // Get the latest 15 deployments
        $raw_sql = 'project_id IN (SELECT id FROM projects WHERE deleted_at IS NULL)';
        return Deployment::whereRaw($raw_sql) // FIXME: Surely there is a nicer way to do this?
                         ->take(15)
                         ->orderBy('started_at', 'DESC')
                         ->get();
    }

    public function getTodayCount(Project $project)
    {
        $now = Carbon::now();

        return $this->getBetweenDates($project, $now, $now);
    }


    public function getLastWeekCount(Project $project)
    {
        $lastWeek  = Carbon::now()->subWeek();
        $yesterday = Carbon::now()->yesterday(); // FIXME: Should this be today?

        return $this->getBetweenDates($project, $lastWeek, $yesterday);
    }

    private function getBetweenDates(Project $project, Carbon $startDate, Carbon $endDate)
    {
        return Deployment::where('project_id', $project->id)
                         ->where('started_at', '>=', $startDate->format('Y-m-d') . ' 00:00:00')
                         ->where('started_at', '<=', $endDate->format('Y-m-d') . ' 23:59:59')
                         ->count();
    }
}