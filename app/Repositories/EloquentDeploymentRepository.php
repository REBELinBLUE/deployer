<?php

namespace REBELinBLUE\Deployer\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\AbortDeployment;
use REBELinBLUE\Deployer\Jobs\QueueDeployment;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;

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
     * Creates a new instance of the model.
     *
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
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
     * @param int $model_id
     *
     *
     * @throws ModelNotFoundException
     */
    public function abort(int $model_id): void
    {
        $deployment = $this->getById($model_id);

        if (!$deployment->isAborting()) {
            $deployment->status = Deployment::ABORTING;
            $deployment->save();

            $this->dispatch(new AbortDeployment($deployment));
        }
    }

    /**
     * @param int $project_id
     */
    public function abortQueued(int $project_id): void
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
     * @param int    $model_id
     * @param string $reason
     * @param array  $optional
     *
     * @throws ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function rollback(int $model_id, string $reason = '', array $optional = [])
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
     * @param int $project_id
     * @param int $paginate
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLatest(int $project_id, int $paginate = 15)
    {
        return $this->model->where('project_id', $project_id)
                           ->with('user', 'project')
                           ->whereNotNull('started_at')
                           ->orderBy('started_at', 'DESC')
                           ->paginate($paginate);
    }

    /**
     * @param int $project_id
     *
     * @return Deployment|null
     */
    public function getLatestSuccessful(int $project_id): ?Deployment
    {
        return $this->model->where('project_id', $project_id)
                           ->where('status', Deployment::COMPLETED)
                           ->whereNotNull('started_at')
                           ->orderBy('started_at', 'DESC')
                           ->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPending()
    {
        return $this->getStatus(Deployment::PENDING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRunning()
    {
        return $this->getStatus(Deployment::DEPLOYING);
    }

    /**
     * @param int $project_id
     *
     * @return int
     */
    public function getTodayCount(int $project_id): int
    {
        $now = Carbon::now();

        return $this->getBetweenDates($project_id, $now, $now);
    }

    /**
     * @param int $project_id
     *
     * @return int
     */
    public function getLastWeekCount(int $project_id): int
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
    private function getBetweenDates(int $project_id, Carbon $startDate, Carbon $endDate): int
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
    private function getStatus(int $status)
    {
        $raw_sql = 'project_id IN (SELECT id FROM projects WHERE deleted_at IS NULL)';

        return $this->model->whereRaw($raw_sql)
                           ->where('status', $status)
                           ->whereNotNull('started_at')
                           ->orderBy('started_at', 'DESC')
                           ->get();
    }
}
