<?php

namespace REBELinBLUE\Deployer\Repositories;

use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Contracts\Repositories\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\AbortDeployment;
use REBELinBLUE\Deployer\Jobs\QueueDeployment;

/**
 * The deployment repository.
 */
class EloquentDeploymentRepository extends EloquentRepository implements DeploymentRepositoryInterface
{
    use DispatchesJobs;

    /**
     * EloquentDeploymentRepository constructor.
     *
     * @param Deployment $model
     */
    public function __construct(Deployment $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $fields)
    {
        $optional = [];
        if (array_key_exists('optional', $fields)) {
            $optional = $fields['optional'];
            unset($fields['optional']);
        }

        $deployment = $this->model->create($fields);

        $this->dispatch(new QueueDeployment(
            $deployment->project,
            $deployment,
            $optional
        ));

        return $deployment;
    }

    /**
     * {@inheritdoc}
     * @dispatches AbortDeployment
     */
    public function abort($model_id)
    {
        $deployment = $this->getById($model_id);

        if (!$deployment->isAborting()) {
            $deployment->status = Deployment::ABORTING;
            $deployment->save();

            $this->dispatch(new AbortDeployment($deployment));
        }
    }

    /**
     * Gets all pending and running deployments for a project and aborts them.
     *
     * {@inheritdoc}
     * @dispatches AbortDeployment
     */
    public function abortQueued($project_id)
    {
        $deployments = $this->model->where('project_id', $project_id)
                                   ->whereIn('status', [Deployment::DEPLOYING, Deployment::PENDING])
                                   ->orderBy('started_at', 'DESC')
                                   ->get();

        foreach ($deployments as $deployment) {
            $deployment->status = Deployment::ABORTING;
            $deployment->save();

            $this->dispatch(new AbortDeployment($deployment));

            if ($deployment->is_webhook) {
                $deployment->delete();
            }
        }
    }

    /**
     * Creates a new deployment based on a previous one.
     *
     * {@inheritdoc}
     */
    public function rollback($model_id, $reason = '', array $optional = [])
    {
        $previous = $this->getById($model_id);

        return $this->create([
            'committer'       => $previous->committer,
            'committer_email' => $previous->committer_email,
            'commit'          => $previous->commit,
            'project_id'      => $previous->project_id,
            'branch'          => $previous->branch,
            'reason'          => $reason,
            'optional'        => $optional,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getLatest($project_id, $paginate = 15)
    {
        return $this->model->where('project_id', $project_id)
                           ->with('user', 'project')
                           ->whereNotNull('started_at')
                           ->orderBy('started_at', 'DESC')
                           ->paginate($paginate);
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestSuccessful($project_id)
    {
        return $this->model->where('project_id', $project_id)
                           ->where('status', Deployment::COMPLETED)
                           ->whereNotNull('started_at')
                           ->orderBy('started_at', 'DESC')
                           ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeline()
    {
        $raw_sql = 'project_id IN (SELECT id FROM projects WHERE deleted_at IS NULL)';

        return $this->model->whereRaw($raw_sql)
                           ->whereNotNull('started_at')
                           ->with('project')
                           ->take(15)
                           ->orderBy('started_at', 'DESC')
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getPending()
    {
        return $this->getStatus(Deployment::PENDING);
    }

    /**
     * {@inheritdoc}
     */
    public function getRunning()
    {
        return $this->getStatus(Deployment::DEPLOYING);
    }

    /**
     * {@inheritdoc}
     * @see DeploymentRepository::getBetweenDates()
     */
    public function getTodayCount($project_id)
    {
        $now = Carbon::now();

        return $this->getBetweenDates($project_id, $now, $now);
    }

    /**
     * {@inheritdoc}
     * @see DeploymentRepository::getBetweenDates()
     */
    public function getLastWeekCount($project_id)
    {
        $lastWeek  = Carbon::now()->subWeek();
        $yesterday = Carbon::now()->yesterday();

        return $this->getBetweenDates($project_id, $lastWeek, $yesterday);
    }

    /**
     * @param int    $project_id
     * @param Carbon $startDate
     * @param Carbon $endDate
     *
     * @return int
     */
    private function getBetweenDates($project_id, Carbon $startDate, Carbon $endDate)
    {
        return $this->model->where('project_id', $project_id)
                           ->where('started_at', '>=', $startDate->format('Y-m-d') . ' 00:00:00')
                           ->where('started_at', '<=', $endDate->format('Y-m-d') . ' 23:59:59')
                           ->count();
    }

    /**
     * @param int $status
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getStatus($status)
    {
        $raw_sql = 'project_id IN (SELECT id FROM projects WHERE deleted_at IS NULL)';

        return $this->model->whereRaw($raw_sql)
                           ->where('status', $status)
                           ->whereNotNull('started_at')
                           ->orderBy('started_at', 'DESC')
                           ->get();
    }
}
