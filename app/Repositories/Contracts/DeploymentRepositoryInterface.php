<?php namespace App\Repositories\Contracts;

use App\Project;

interface DeploymentRepositoryInterface
{
    public function getLatest(Project $project);
    public function getTimeline();
    public function getTodayCount(Project $project);
    public function getLastWeekCount(Project $project);
}
